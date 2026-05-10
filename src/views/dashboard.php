<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Loopbook</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><defs><linearGradient id=%22g%22 x1=%220%22 y1=%220%22 x2=%2240%22 y2=%2240%22 gradientUnits=%22userSpaceOnUse%22><stop offset=%220%25%22 stop-color=%22%23cc0000%22/><stop offset=%2255%25%22 stop-color=%22%23ff2800%22/><stop offset=%22100%25%22 stop-color=%22%23ff6b00%22/></linearGradient></defs><rect width=%2240%22 height=%2240%22 rx=%2211%22 fill=%22url(%23g)%22/><text x=%2250%25%22 y=%2257%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2213%22 font-weight=%22700%22 fill=%22white%22>%3C/%3E</text></svg>">
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<main class="main-content">
<div class="page-container">

  <div class="dashboard-header">
    <div>
      <h1 class="page-title gradient-text">¡Hola, <?= e($_SESSION['nombre'] ?? $_SESSION['username'] ?? 'estudiante') ?>! 👋</h1>
      <p class="page-subtitle">✨ Selecciona un módulo para continuar tu viaje</p>
    </div>
    <div class="progress-badge">
      <span class="progress-badge__icon">🏆</span>
      <span class="progress-badge__value"><?= $overallProgress ?>%</span>
      <span class="progress-badge__label">Progreso Total</span>
    </div>
  </div>

  <div class="modules-grid">
    <?php
    $gradients = ['red-orange', 'blue-cyan', 'green-emerald', 'violet-blue', 'amber-yellow'];
    $icons     = ['💻', '⚙️', '🗄️', '🔀', '🧩'];
    foreach ($modules as $i => $module):
      $mid    = (int)$module['id_modulo'];
      $grad   = $gradients[$i % count($gradients)];
      $icon   = $icons[$i % count($icons)];
      $pData  = $progressData[$mid];
      $isDone = $pData['percent'] === 100;
    ?>
    <div class="module-card">
      <?php if ($isDone): ?>
        <span class="module-card__done-badge">✅</span>
      <?php endif; ?>
      <div class="module-card__stripe module-card__stripe--<?= $grad ?>"></div>
      <div class="module-card__body">
        <div class="module-card__top">
          <div class="module-card__icon module-card__icon--<?= $grad ?>"><?= $icon ?></div>
          <div class="module-card__info">
            <h3 class="module-card__title"><?= e($module['nombre']) ?></h3>
            <p class="module-card__desc"><?= e($module['descripcion']) ?></p>
          </div>
        </div>
        <div class="module-card__bottom">
          <div class="module-card__progress">
            <div class="progress-row">
              <span>Ejercicios completados</span>
              <strong><?= $pData['completed'] ?>/<?= $pData['total'] ?> (<?= $pData['percent'] ?>%)</strong>
            </div>
            <div class="progress-bar">
              <div class="progress-bar__fill progress-bar__fill--<?= $grad ?>"
                   style="width:<?= $pData['percent'] ?>%"></div>
            </div>
          </div>
          <div class="module-card__footer">
            <span class="module-card__lessons">📚 <?= $pData['total'] ?> ejercicios</span>
            <a href="<?= base_url('index.php?page=module&id=' . $mid) ?>"
               class="btn btn-sm btn-gradient btn-gradient--<?= $grad ?>">
              <?= $pData['percent'] > 0 ? 'Continuar' : 'Comenzar' ?> →
            </a>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

</div>
</main>
<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
