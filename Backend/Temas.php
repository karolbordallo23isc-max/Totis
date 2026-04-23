<?php
session_start();
require_once __DIR__ . "/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: iniciarSesion.php");
    exit();
}

$id_usuario    = $_SESSION["id_usuario"];
$id_modulo_sel = isset($_GET["modulo"]) ? (int)$_GET["modulo"] : 0;

if ($id_modulo_sel <= 0) {
    header("Location: Cursos.php");
    exit();
}

// Obtener info del módulo seleccionado
$stmtMod = $pdo->prepare("
    SELECT m.id_modulo, m.nombre, m.descripcion, c.nombre AS nombre_curso
    FROM modulos m
    JOIN curso c ON m.id_curso = c.id_curso
    WHERE m.id_modulo = ?
");
$stmtMod->execute([$id_modulo_sel]);
$modulo = $stmtMod->fetch(PDO::FETCH_ASSOC);

if (!$modulo) {
    header("Location: Cursos.php");
    exit();
}

// Obtener ejercicios del módulo
$stmtEj = $pdo->prepare("SELECT * FROM ejercicios WHERE id_modulo = ? ORDER BY id_ejercicio");
$stmtEj->execute([$id_modulo_sel]);
$ejercicios = $stmtEj->fetchAll(PDO::FETCH_ASSOC);

// Verificar cuáles ejercicios están completados por el usuario
$completados_ids = [];
if (!empty($ejercicios)) {
    $ids = array_column($ejercicios, "id_ejercicio");
    $ph  = implode(",", array_fill(0, count($ids), "?"));
    $stmtC = $pdo->prepare("
        SELECT id_ejercicio FROM progreso
        WHERE id_usuario = ? AND id_ejercicio IN ($ph) AND completado = 1
    ");
    $stmtC->execute(array_merge([$id_usuario], $ids));
    $completados_ids = $stmtC->fetchAll(PDO::FETCH_COLUMN);
}

// Calcular progreso del módulo
$total_ej   = count($ejercicios);
$total_comp = count($completados_ids);
$pct_modulo = $total_ej > 0 ? round(($total_comp / $total_ej) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoopBook - <?= htmlspecialchars($modulo["nombre"]) ?></title>
    <style>
        :root { --sidebar-width: 220px; }
        body { margin: 0; font-family: Arial, sans-serif; display: flex; height: 100vh; }
        .sidebar { width: var(--sidebar-width); background-color: black; color: white; padding: 25px 20px; flex-shrink: 0; }
        .sidebar h2 { margin: 0 0 10px 0; font-size: 1.2rem; cursor: pointer; }
        .sidebar p  { margin: 5px 0; font-size: 1rem; cursor: pointer; }
        .sidebar h2:hover, .sidebar p:hover { color: #4db6ac; }
        .main-content { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; border-bottom: 1px solid #ddd; }
        .logo { font-weight: bold; font-size: 1.5rem; }
        .top-nav { display: flex; gap: 25px; align-items: center; }
        .top-nav span { font-weight: bold; font-size: 0.9rem; cursor: pointer; }
        .lessons-container { padding: 20px 60px; max-width: 900px; margin: 0 auto; width: 100%; box-sizing: border-box; text-align: center; }
        .section-title { font-size: 1.8rem; margin-bottom: 10px; }
        .section-sub { color: #666; font-size: 0.9rem; margin-bottom: 10px; }

        .modulo-progreso { margin: 0 auto 35px auto; max-width: 500px; }
        .prog-info { display: flex; justify-content: space-between; font-size: 0.82rem; color: #666; margin-bottom: 5px; }
        .prog-bar { background: #e0e0e0; height: 10px; border-radius: 5px; overflow: hidden; }
        .prog-fill { background: #4db6ac; height: 100%; transition: width 1s ease-in-out; }
        .prog-fill.done { background: #27ae60; }

        .lesson-card {
            border: 1.5px solid #333;
            border-radius: 20px;
            padding: 30px 40px;
            margin-bottom: 30px;
            text-align: left;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: box-shadow 0.2s, border-color 0.2s;
        }
        .lesson-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.12); border-color: #0091ff; }
        .lesson-card.completada { border-color: #27ae60; background: #f6fff9; }
        .lesson-number { font-size: 0.9rem; color: #666; margin-bottom: 8px; }
        .lesson-title  { font-size: 1.3rem; font-weight: bold; margin: 0 0 12px 0; }
        .lesson-text   { font-size: 1rem; line-height: 1.5; color: #444; margin-bottom: 20px; }
        .card-footer   { display: flex; justify-content: flex-end; align-items: center; }
        .estado-lec { font-size: 0.85rem; font-weight: bold; padding: 6px 18px; border-radius: 20px; }
        .estado-lec.nueva    { background: #e0f2fe; color: #0091ff; }
        .estado-lec.hecha    { background: #d4edda; color: #27ae60; }
        .estado-lec.progreso { background: #fff3cd; color: #e67e22; }
        .btn-cerrar { background: none; border: none; cursor: pointer; font-weight: bold; font-size: 0.9rem; }
        .btn-cerrar:hover { color: #c0392b; }
    </style>
</head>
<body>

<aside class="sidebar">
    <h2 onclick="window.location.href='Cursos.php'">Inicio</h2>
    <p onclick="window.location.href='Cursos.php'">Resultados</p>
</aside>

<main class="main-content">
    <header class="header">
        <div class="logo"><img src="loopbook_logo.png" alt="LoopBook" style="height:60px;"></div>
        <nav class="top-nav">
            <span onclick="window.location.href='Cursos.php'">inicio</span>
            <form method="POST" action="cerrarSesion.php" style="margin:0;">
                <button type="submit" class="btn-cerrar">cerrar sesión</button>
            </form>
        </nav>
    </header>

    <section class="lessons-container">
        <h1 class="section-title"><?= htmlspecialchars($modulo["nombre"]) ?></h1>
        <p class="section-sub"><?= htmlspecialchars($modulo["descripcion"]) ?></p>

        <div class="modulo-progreso">
            <div class="prog-info">
                <span>Progreso del módulo</span>
                <span id="pct-label">0%</span>
            </div>
            <div class="prog-bar">
                <div class="prog-fill <?= $pct_modulo >= 100 ? 'done' : '' ?>" id="prog-fill"></div>
            </div>
        </div>

        <?php if (empty($ejercicios)): ?>
            <p style="color:#999; font-style:italic;">No hay ejercicios disponibles para este módulo aún.</p>
        <?php else: ?>
            <a class="lesson-card <?= $pct_modulo >= 100 ? 'completada' : '' ?>"
               href="actividades.php?modulo=<?= $id_modulo_sel ?>">
                <div class="lesson-number">
                    <?= $total_ej ?> ejercicio<?= $total_ej !== 1 ? 's' : '' ?> &nbsp;·&nbsp;
                    <?= $total_comp ?> respondido<?= $total_comp !== 1 ? 's' : '' ?>
                </div>
                <h2 class="lesson-title"><?= htmlspecialchars($modulo["nombre"]) ?></h2>
                <p class="lesson-text"><?= htmlspecialchars($modulo["descripcion"]) ?></p>
                <div class="card-footer">
                    <?php if ($pct_modulo >= 100): ?>
                        <span class="estado-lec hecha">✔ Completado</span>
                    <?php elseif ($pct_modulo > 0): ?>
                        <span class="estado-lec progreso">▶ Continuar</span>
                    <?php else: ?>
                        <span class="estado-lec nueva">→ Comenzar</span>
                    <?php endif; ?>
                </div>
            </a>
        <?php endif; ?>
    </section>
</main>

<script>
    const pct = <?= $pct_modulo ?>;
    setTimeout(() => {
        document.getElementById('prog-fill').style.width = pct + '%';
        document.getElementById('pct-label').innerText = pct + '%';
    }, 300);
</script>

</body>
</html>