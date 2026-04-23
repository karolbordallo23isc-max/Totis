<?php
session_start();
require_once __DIR__ . "/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: iniciarSesion.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];

// Obtener todos los módulos ordenados
$stmtModulos = $pdo->query("SELECT id_modulo, nombre, descripcion FROM modulos ORDER BY orden");
$modulos = $stmtModulos->fetchAll(PDO::FETCH_ASSOC);

// Calcular progreso real por módulo
$progreso_modulos = [];
foreach ($modulos as $mod) {
    $id_modulo = $mod["id_modulo"];

    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM ejercicios WHERE id_modulo = ?");
    $stmtTotal->execute([$id_modulo]);
    $total = (int) $stmtTotal->fetchColumn();

    $stmtComp = $pdo->prepare("
        SELECT COUNT(*) FROM progreso p
        JOIN ejercicios e ON p.id_ejercicio = e.id_ejercicio
        WHERE p.id_usuario = ? AND e.id_modulo = ? AND p.completado = 1
    ");
    $stmtComp->execute([$id_usuario, $id_modulo]);
    $completados = (int) $stmtComp->fetchColumn();

    $porcentaje = ($total > 0) ? round(($completados / $total) * 100) : 0;

    $progreso_modulos[$id_modulo] = [
        "total"       => $total,
        "completados" => $completados,
        "porcentaje"  => $porcentaje,
    ];
}

// Íconos por defecto — se usa el módulo como índice, con fallback genérico
$iconos = [1 => "📙", 2 => "🛢️", 3 => "💻", 4 => "🕸️", 5 => "⚙️"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoopBook - Módulos</title>
    <style>
        :root {
            --sidebar-width: 200px;
            --bar-bg: #e0e0e0;
            --bar-fill: #4db6ac;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            height: 100vh;
            background-color: #fff;
        }
        .sidebar {
            width: var(--sidebar-width);
            background-color: #000;
            color: #fff;
            padding: 20px;
            flex-shrink: 0;
        }
        .sidebar h2, .sidebar p { margin: 10px 0; cursor: pointer; }
        .sidebar h2:hover, .sidebar p:hover { color: #4db6ac; }
        .main { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            flex-shrink: 0;
        }
        .logo img { height: 60px; display: block; }
        .content { padding: 20px; text-align: center; }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            max-width: 1000px;
            margin: 20px auto;
        }
        .card {
            background: #f1f1f1;
            padding: 20px;
            border-radius: 8px;
            text-align: left;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.13);
            background: #e8f7f5;
        }
        .card.completada { border-left: 4px solid #27ae60; }
        .card h3 { margin: 0 0 6px 0; font-size: 1.1rem; }
        .desc { font-size: 0.85rem; color: #555; margin-bottom: 15px; min-height: 28px; }
        .progress-box { margin-bottom: 12px; }
        .progress-info { display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 5px; }
        .progress-container {
            background: var(--bar-bg);
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
        }
        .progress-fill {
            background: var(--bar-fill);
            height: 100%;
            width: 0%;
            transition: width 1s ease-in-out;
        }
        .progress-fill.done { background: #27ae60; }
        .footer-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }
        .lecciones-info { font-size: 0.82rem; color: #666; }
        .estado-badge {
            font-size: 0.8rem;
            font-weight: bold;
            padding: 4px 12px;
            border-radius: 20px;
        }
        .estado-badge.completado { background: #d4edda; color: #27ae60; }
        .estado-badge.en-progreso { background: #fff3cd; color: #e67e22; }
        .estado-badge.nuevo { background: #e0f7f5; color: #4db6ac; }
        .btn-cerrar {
            background: none;
            border: none;
            cursor: pointer;
            font-weight: bold;
            font-size: 1rem;
            color: #000;
        }
        .btn-cerrar:hover { color: #c0392b; }
        .bienvenida { color: #666; font-size: 0.9rem; margin-bottom: 5px; }
        .sin-modulos { color: #999; font-style: italic; margin-top: 40px; }
    </style>
</head>
<body>

<aside class="sidebar">
    <h2 onclick="window.location.href='Cursos.php'">Inicio</h2>
    <p onclick="window.location.href='Cursos.php'">Resultados</p>
</aside>

<main class="main">
    <header class="header">
        <div class="logo"><img src="loopbook_logo.png" alt="LoopBook"></div>
        <div style="display:flex; align-items:center; gap:20px;">
            <span>👋 Hola, <?= htmlspecialchars($_SESSION["nombre"]) ?></span>
            <form method="POST" action="cerrarSesion.php" style="margin:0;">
                <button type="submit" class="btn-cerrar">cerrar sesión</button>
            </form>
        </div>
    </header>

    <section class="content">
        <h2 style="text-align:center; margin-bottom:5px;">Módulos</h2>
        <p class="bienvenida">Selecciona el módulo de tu interés o reanuda tu progreso</p>

        <?php if (empty($modulos)): ?>
            <p class="sin-modulos">No hay módulos disponibles aún.</p>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($modulos as $mod):
                    $id    = $mod["id_modulo"];
                    $p     = $progreso_modulos[$id];
                    $pct   = $p["porcentaje"];
                    $icono = $iconos[$id] ?? "📘";
                    $done  = $pct >= 100;
                    $enProgreso = $pct > 0 && !$done;
                ?>
                <a class="card <?= $done ? 'completada' : '' ?>"
                   href="Temas.php?modulo=<?= $id ?>"
                   data-progreso="<?= $pct ?>">
                    <h3><?= $icono ?> <?= htmlspecialchars($mod["nombre"]) ?></h3>
                    <p class="desc"><?= htmlspecialchars($mod["descripcion"]) ?></p>
                    <div class="progress-box">
                        <div class="progress-info">
                            <span>Progreso</span>
                            <span class="pct">0%</span>
                        </div>
                        <div class="progress-container">
                            <div class="progress-fill <?= $done ? 'done' : '' ?>"></div>
                        </div>
                    </div>
                    <div class="footer-card">
                        <span class="lecciones-info">
                            <?= $p["completados"] ?>/<?= $p["total"] ?> ejercicios completados
                        </span>
                        <?php if ($done): ?>
                            <span class="estado-badge completado">✔ Completado</span>
                        <?php elseif ($enProgreso): ?>
                            <span class="estado-badge en-progreso">▶ Continuar</span>
                        <?php else: ?>
                            <span class="estado-badge nuevo">→ Iniciar</span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<script>
    document.querySelectorAll('.card').forEach(card => {
        const pct = parseInt(card.getAttribute('data-progreso')) || 0;
        const barra = card.querySelector('.progress-fill');
        const textoPct = card.querySelector('.pct');
        setTimeout(() => {
            barra.style.width = pct + '%';
            textoPct.innerText = pct + '%';
        }, 300);
    });
</script>
</body>
</html>