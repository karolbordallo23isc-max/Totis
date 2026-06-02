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

  <?php
  /* Mostrar mensaje de módulo bloqueado si viene del ModuleController */
  $lockMsg = $_SESSION['admin_error'] ?? '';
  unset($_SESSION['admin_error']);
  if ($lockMsg):
  ?>
  <div class="alert-lock">
    <span class="alert-lock__icon">🔒</span>
    <span><?= e($lockMsg) ?></span>
  </div>
  <?php endif; ?>

  <div class="dashboard-header">
    <div>
      <h1 class="page-title gradient-text">
        ¡Hola, <?= e($_SESSION['nombre'] ?? $_SESSION['username'] ?? 'estudiante') ?>! 👋
      </h1>
      <p class="page-subtitle">Completa cada módulo en orden para desbloquear el siguiente</p>
    </div>
    <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
      <div class="progress-badge">
        <span class="progress-badge__icon">🏆</span>
        <span class="progress-badge__value"><?= $overallProgress ?>%</span>
        <span class="progress-badge__label">Progreso Total</span>
      </div>
    </div>
  </div>

  <!-- Barra de progreso global -->
  <div class="global-progress-bar mb-4">
    <div class="global-progress-bar__fill" style="width:<?= $overallProgress ?>%"></div>
  </div>

  <div class="modules-grid">
    <?php
    $gradients = ['red-orange', 'blue-cyan', 'green-emerald', 'violet-blue', 'amber-yellow'];
    $icons     = ['💻', '⚙️', '🗄️', '🔀', '🧩'];

    foreach ($modules as $i => $module):
      $mid      = (int)$module['id_modulo'];
      $grad     = $gradients[$i % count($gradients)];
      $icon     = $icons[$i % count($icons)];
      $pData    = $progressData[$mid];
      $isDone   = $pData['percent'] === 100;
      $unlocked = $pData['unlocked'] ?? true;
      $isFirst  = $i === 0;
    ?>

    <div class="module-card <?= !$unlocked ? 'module-card--locked' : '' ?> <?= $isDone ? 'module-card--done' : '' ?>">

      <?php if ($isDone): ?>
        <span class="module-card__done-badge">✅</span>
      <?php elseif (!$unlocked): ?>
        <span class="module-card__lock-badge">🔒</span>
      <?php endif; ?>

      <!-- Overlay de bloqueo -->
      <?php if (!$unlocked): ?>
      <div class="module-card__lock-overlay">
        <div class="module-card__lock-content">
          <span class="module-card__lock-icon">🔒</span>
          <p class="module-card__lock-msg">Completa el módulo anterior</p>
        </div>
      </div>
      <?php endif; ?>

      <div class="module-card__stripe module-card__stripe--<?= $grad ?>"></div>

      <div class="module-card__body">
        <div class="module-card__top">
          <div class="module-card__icon module-card__icon--<?= $grad ?>">
            <?= $unlocked ? $icon : '🔒' ?>
          </div>
          <div class="module-card__info">
            <span class="module-card__num">Módulo <?= $i + 1 ?></span>
            <h3 class="module-card__title"><?= e($module['nombre']) ?></h3>
            <p class="module-card__desc"><?= e($module['descripcion'] ?? '') ?></p>
          </div>
        </div>

        <div class="module-card__bottom">
          <?php if ($unlocked): ?>
          <div class="module-card__progress">
            <div class="progress-row">
              <span>Progreso actual</span>
              <strong><?= $pData['completed'] ?>/<?= $pData['total'] ?> (<?= $pData['percent'] ?>%)</strong>
            </div>
            <div class="progress-bar">
              <div class="progress-bar__fill progress-bar__fill--<?= $grad ?>"
                   style="width:<?= $pData['percent'] ?>%"></div>
            </div>
            <?php if ($pData['attempts'] > 0): ?>
            <div style="display:flex;gap:1rem;margin-top:.4rem;font-size:.78rem;color:var(--gray-500)">
              <span>🔁 <?= $pData['attempts'] ?> intento<?= $pData['attempts'] !== 1 ? 's' : '' ?> totales</span>
              <?php if ($pData['ever_completed'] > 0): ?>
              <span>✅ <?= $pData['ever_completed'] ?>/<?= $pData['total'] ?> completados alguna vez</span>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
          <?php else: ?>
          <div class="module-card__locked-info">
            <span>🔐 Bloqueado — termina el módulo <?= $i ?> primero</span>
          </div>
          <?php endif; ?>

          <div class="module-card__footer">
            <span class="module-card__lessons">
              📚 <?= $pData['total'] ?> ejercicios
            </span>

            <?php if ($unlocked): ?>
              <a href="<?= base_url('index.php?page=module&id=' . $mid) ?>"
                 class="btn btn-sm btn-gradient btn-gradient--<?= $grad ?>">
                <?php
                  if ($isDone)          echo '🔁 Repasar →';
                  elseif ($pData['percent'] > 0) echo '▶️ Continuar →';
                  else                  echo '🚀 Comenzar →';
                ?>
              </a>
            <?php else: ?>
              <button class="btn btn-sm btn-locked" disabled title="Completa el módulo anterior para desbloquear">
                🔒 Bloqueado
              </button>
            <?php endif; ?>
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
