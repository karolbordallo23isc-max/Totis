<?php
session_start();
require_once __DIR__ . "/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: iniciarSesion.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$id_modulo  = isset($_GET["modulo"]) ? (int)$_GET["modulo"] : 1;
$mensaje    = "";
$tipo_msg   = "";

// Guardar o actualizar respuesta abierta
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id_ejercicio"], $_POST["respuesta"])) {
    $id_ejercicio = (int)$_POST["id_ejercicio"];
    $respuesta    = trim($_POST["respuesta"]);

    if (!empty($respuesta)) {
        $fecha = date("Y-m-d H:i:s");
        $stmt = $pdo->prepare("
            INSERT INTO respuesta_abierta (id_usuario, id_ejercicio, respuesta, fecha_respuesta)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE respuesta = VALUES(respuesta), fecha_respuesta = VALUES(fecha_respuesta)
        ");
        $stmt->execute([$id_usuario, $id_ejercicio, $respuesta, $fecha]);
        $mensaje  = "✅ Respuesta guardada correctamente.";
        $tipo_msg = "exito";
    } else {
        $mensaje  = "⚠️ No puedes guardar una respuesta vacía.";
        $tipo_msg = "error";
    }
}

// Obtener ejercicios del módulo
$stmtEj = $pdo->prepare("SELECT * FROM ejercicios WHERE id_modulo = ? ORDER BY id_ejercicio");
$stmtEj->execute([$id_modulo]);
$ejercicios = $stmtEj->fetchAll(PDO::FETCH_ASSOC);
$total_ejercicios = count($ejercicios);

// Obtener respuestas previas del usuario
$respuestas_previas = [];
$ids_ejercicios = [];
if (!empty($ejercicios)) {
    $ids_ejercicios = array_column($ejercicios, "id_ejercicio");
    $placeholders = implode(",", array_fill(0, count($ids_ejercicios), "?"));
    $stmtResp = $pdo->prepare("SELECT id_ejercicio, respuesta FROM respuesta_abierta WHERE id_usuario = ? AND id_ejercicio IN ($placeholders)");
    $stmtResp->execute(array_merge([$id_usuario], $ids_ejercicios));
    foreach ($stmtResp->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $respuestas_previas[$r["id_ejercicio"]] = $r["respuesta"];
    }
}

// Marcar lección como completada — solo si respondió TODOS los ejercicios
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["completar_leccion"])) {
    $respondidos = count($respuestas_previas);

    if ($total_ejercicios > 0 && $respondidos < $total_ejercicios) {
        $faltantes = $total_ejercicios - $respondidos;
        $mensaje  = "⚠️ Aún te faltan $faltantes ejercicio(s) por responder antes de completar la lección.";
        $tipo_msg = "error";
    } else {
        $fecha = date("Y-m-d H:i:s");
        foreach ($ids_ejercicios as $id_ej) {
            $stmtProg = $pdo->prepare("
                INSERT INTO progreso (id_usuario, id_ejercicio, intentos, calificacion, completado, fecha_progreso)
                VALUES (?, ?, 1, 100, 1, ?)
                ON DUPLICATE KEY UPDATE completado = 1, intentos = intentos + 1, fecha_progreso = VALUES(fecha_progreso)
            ");
            $stmtProg->execute([$id_usuario, $id_ej, $fecha]);
        }
        $mensaje  = "🎉 ¡Lección completada! Tu progreso ha sido actualizado.";
        $tipo_msg = "exito";
    }
}

// Obtener info del módulo
$stmtModulo = $pdo->prepare("SELECT m.nombre, m.descripcion, c.nombre AS nombre_curso
                              FROM modulos m
                              JOIN curso c ON m.id_curso = c.id_curso
                              WHERE m.id_modulo = ?");
$stmtModulo->execute([$id_modulo]);
$modulo = $stmtModulo->fetch(PDO::FETCH_ASSOC);

// Verificar si la lección ya está completada
$ya_completados = 0;
if (!empty($ids_ejercicios)) {
    $placeholders2 = implode(",", array_fill(0, count($ids_ejercicios), "?"));
    $stmtCheck = $pdo->prepare("
        SELECT COUNT(*) FROM progreso
        WHERE id_usuario = ? AND id_ejercicio IN ($placeholders2) AND completado = 1
    ");
    $stmtCheck->execute(array_merge([$id_usuario], $ids_ejercicios));
    $ya_completados = (int) $stmtCheck->fetchColumn();
}
$leccion_completada = ($total_ejercicios > 0 && $ya_completados >= $total_ejercicios);
$total_respondidos  = count($respuestas_previas);

// Obtener contenido del módulo
$stmtContenido = $pdo->prepare("SELECT * FROM contenido WHERE id_modulo = ? ORDER BY orden");
$stmtContenido->execute([$id_modulo]);
$contenidos = $stmtContenido->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoopBook - Lección Interactiva</title>
    <style>
        :root { --sidebar-width: 210px; --text-gray: #666; --primary-blue: #0091ff; --btn-teal: #4db6ac; }
        body { margin: 0; font-family: Arial, sans-serif; display: flex; height: 100vh; background-color: white; }
        .sidebar { width: var(--sidebar-width); background-color: black; color: white; padding: 30px 20px; flex-shrink: 0; }
        .sidebar h2 { margin: 0 0 5px 0; font-size: 1.2rem; cursor: pointer; }
        .sidebar p  { margin: 5px 0; font-size: 1.1rem; cursor: pointer; }
        .sidebar h2:hover, .sidebar p:hover { color: #4db6ac; }
        .main-content { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; }
        header { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; border-bottom: 1px solid #ddd; }
        .logo { font-weight: bold; font-size: 1.5rem; }
        .top-nav { display: flex; gap: 25px; align-items: center; font-weight: bold; }
        .lesson-container { padding: 20px 60px; max-width: 900px; }
        .lesson-title { font-size: 1.8rem; margin: 0; }
        .category { color: var(--text-gray); margin-top: 10px; margin-bottom: 40px; }
        .section-header { font-size: 1.4rem; text-align: center; margin-bottom: 20px; font-weight: bold; }
        .content-text { line-height: 1.6; color: #333; font-size: 1.05rem; margin-bottom: 40px; }
        .practice-title { font-size: 1.4rem; font-weight: bold; margin-bottom: 5px; }
        .practice-subtitle { color: var(--text-gray); font-size: 0.9rem; margin-bottom: 35px; }
        .question-box { margin-bottom: 35px; background: #f9f9f9; padding: 20px; border-radius: 10px; border: 1px solid #eee; }
        .question-text { font-weight: bold; font-size: 1.1rem; display: block; margin-bottom: 12px; }
        textarea { width: 100%; border: 1px solid #ccc; border-radius: 8px; padding: 12px; font-family: Arial, sans-serif; font-size: 1rem; resize: vertical; box-sizing: border-box; background-color: #fff; }
        textarea:focus { outline: none; border-color: var(--primary-blue); }
        .btn-guardar { background-color: var(--primary-blue); color: white; border: none; padding: 9px 25px; border-radius: 7px; font-size: 0.95rem; font-weight: bold; cursor: pointer; margin-top: 12px; }
        .btn-guardar:hover { background-color: #0076d1; }
        .guardado-label { font-size: 0.8rem; color: #27ae60; margin-top: 6px; display: block; }
        .msg { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.95rem; }
        .msg.exito { background: #eafaf1; border-left: 4px solid #27ae60; color: #1e8449; }
        .msg.error { background: #fdecea; border-left: 4px solid #e74c3c; color: #c0392b; }
        .sin-ejercicios { text-align: center; color: #999; font-style: italic; margin: 40px 0; }
        .btn-cerrar { background: none; border: none; cursor: pointer; font-weight: bold; font-size: 1rem; }
        .btn-cerrar:hover { color: #c0392b; }

        .completar-box {
            margin-top: 40px;
            margin-bottom: 40px;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid #4db6ac;
            background: #f0faf8;
        }
        .completar-box p { margin: 0 0 15px 0; color: #555; font-size: 0.95rem; }
        .completar-box .aviso-respuestas { font-size: 0.85rem; color: #e67e22; margin-bottom: 12px; }
        .btn-completar { background-color: var(--btn-teal); color: white; border: none; padding: 12px 35px; border-radius: 8px; font-size: 1rem; font-weight: bold; cursor: pointer; transition: background 0.2s; }
        .btn-completar:hover { background-color: #3a9e95; }
        .btn-completar:disabled { background-color: #ccc; cursor: not-allowed; }
        .completado-badge { display: inline-block; background: #27ae60; color: white; padding: 10px 30px; border-radius: 8px; font-size: 1rem; font-weight: bold; margin-bottom: 12px; }
    </style>
</head>
<body>

<aside class="sidebar">
    <h2 onclick="window.location.href='Cursos.php'">Inicio</h2>
    <p onclick="window.location.href='Cursos.php'">Resultados</p>
</aside>

<main class="main-content">
    <header>
        <div class="logo">LoopBook</div>
        <nav class="top-nav">
            <span style="cursor:pointer" onclick="window.location.href='Cursos.php'"> inicio</span>
            <form method="POST" action="cerrarSesion.php" style="margin:0;">
                <button type="submit" class="btn-cerrar">cerrar sesión</button>
            </form>
        </nav>
    </header>

    <section class="lesson-container">
        <?php if ($modulo): ?>
            <h1 class="lesson-title"><?= htmlspecialchars($modulo["nombre"]) ?></h1>
            <p class="category"><?= htmlspecialchars($modulo["nombre_curso"]) ?></p>
        <?php else: ?>
            <h1 class="lesson-title">Lección</h1>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <div class="msg <?= $tipo_msg ?>"><?= $mensaje ?></div>
        <?php endif; ?>

        <?php if (!empty($contenidos)): ?>
            <div class="section-header">Contenido</div>
            <?php foreach ($contenidos as $c): ?>
                <p class="content-text"><?= htmlspecialchars($c["texto"]) ?></p>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="practice-title">Preguntas de práctica</div>
        <p class="practice-subtitle">Escribe tu respuesta y guárdala. Puedes modificarla cuando quieras.</p>

        <?php if (empty($ejercicios)): ?>
            <p class="sin-ejercicios">No hay ejercicios disponibles para este módulo aún.</p>
        <?php else: ?>
            <?php foreach ($ejercicios as $i => $ej): ?>
                <?php $respuesta_previa = $respuestas_previas[$ej["id_ejercicio"]] ?? ""; ?>
                <div class="question-box">
                    <span class="question-text"><?= ($i + 1) ?>. <?= htmlspecialchars($ej["pregunta"]) ?></span>
                    <form method="POST" action="actividades.php?modulo=<?= $id_modulo ?>">
                        <input type="hidden" name="id_ejercicio" value="<?= $ej["id_ejercicio"] ?>">
                        <textarea name="respuesta" rows="3" placeholder="Escribe tu respuesta aquí..."><?= htmlspecialchars($respuesta_previa) ?></textarea>
                        <?php if ($respuesta_previa): ?>
                            <span class="guardado-label">✔ Respuesta guardada — puedes editarla y volver a guardar</span>
                        <?php endif; ?>
                        <button type="submit" class="btn-guardar">
                            <?= $respuesta_previa ? "Actualizar respuesta" : "Guardar respuesta" ?>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>

            <div class="completar-box">
                <?php if ($leccion_completada): ?>
                    <div class="completado-badge">✔ Lección completada</div>
                    <p style="margin:10px 0 0;">¡Ya completaste esta lección! Puedes seguir repasando cuando quieras.</p>
                <?php else: ?>
                    <p>Cuando termines todos los ejercicios, marca la lección como completada para actualizar tu progreso.</p>
                    <?php if ($total_respondidos < $total_ejercicios): ?>
                        <p class="aviso-respuestas">
                            ⚠️ Tienes <?= $total_respondidos ?>/<?= $total_ejercicios ?> respuestas guardadas. Responde todas para poder completar.
                        </p>
                    <?php endif; ?>
                    <form method="POST" action="actividades.php?modulo=<?= $id_modulo ?>">
                        <input type="hidden" name="completar_leccion" value="1">
                        <button type="submit" class="btn-completar" <?= $total_respondidos < $total_ejercicios ? 'disabled' : '' ?>>
                            ✔ Marcar lección como completada
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

</body>
</html>