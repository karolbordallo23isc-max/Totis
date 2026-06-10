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

    // SVGs por categoría — iconos profesionales compactos
    $moduleSvgs = [
      'Fundamentos' => '<svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" rx="10" fill="currentColor" fill-opacity=".12"/><path d="M8 14 L15 20 L8 26" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill-opacity="0"/><line x1="18" y1="26" x2="28" y2="26" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" opacity=".7"/><rect x="22" y="9" width="11" height="8" rx="2.5" fill="currentColor" fill-opacity=".25" stroke="currentColor" stroke-width="1.5"/><line x1="24" y1="12" x2="31" y2="12" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" opacity=".6"/><line x1="24" y1="14.5" x2="29" y2="14.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" opacity=".4"/></svg>',

      'POO' => '<svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" rx="10" fill="currentColor" fill-opacity=".12"/><rect x="13" y="6" width="14" height="9" rx="3" fill="currentColor" fill-opacity=".35" stroke="currentColor" stroke-width="1.4"/><line x1="20" y1="15" x2="20" y2="20" stroke="currentColor" stroke-width="1.5" opacity=".6"/><line x1="11" y1="20" x2="29" y2="20" stroke="currentColor" stroke-width="1.5" opacity=".5"/><line x1="11" y1="20" x2="11" y2="24" stroke="currentColor" stroke-width="1.5" opacity=".5"/><line x1="29" y1="20" x2="29" y2="24" stroke="currentColor" stroke-width="1.5" opacity=".5"/><rect x="5" y="24" width="12" height="8" rx="2.5" fill="currentColor" fill-opacity=".3" stroke="currentColor" stroke-width="1.3"/><rect x="23" y="24" width="12" height="8" rx="2.5" fill="currentColor" fill-opacity=".3" stroke="currentColor" stroke-width="1.3"/></svg>',

      'Estructuras' => '<svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" rx="10" fill="currentColor" fill-opacity=".12"/><circle cx="20" cy="8" r="4" fill="currentColor" fill-opacity=".45"/><circle cx="10" cy="22" r="3.5" fill="currentColor" fill-opacity=".4"/><circle cx="30" cy="22" r="3.5" fill="currentColor" fill-opacity=".4"/><circle cx="6" cy="34" r="3" fill="currentColor" fill-opacity=".3"/><circle cx="16" cy="34" r="3" fill="currentColor" fill-opacity=".3"/><circle cx="28" cy="34" r="3" fill="currentColor" fill-opacity=".3"/><circle cx="36" cy="34" r="2.5" fill="currentColor" fill-opacity=".25"/><line x1="20" y1="12" x2="10" y2="18.5" stroke="currentColor" stroke-width="1.5" opacity=".5"/><line x1="20" y1="12" x2="30" y2="18.5" stroke="currentColor" stroke-width="1.5" opacity=".5"/><line x1="10" y1="25.5" x2="6" y2="31" stroke="currentColor" stroke-width="1.3" opacity=".4"/><line x1="10" y1="25.5" x2="16" y2="31" stroke="currentColor" stroke-width="1.3" opacity=".4"/><line x1="30" y1="25.5" x2="28" y2="31" stroke="currentColor" stroke-width="1.3" opacity=".4"/><line x1="30" y1="25.5" x2="36" y2="31" stroke="currentColor" stroke-width="1.3" opacity=".4"/></svg>',

      'Bases de Datos' => '<svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" rx="10" fill="currentColor" fill-opacity=".12"/><ellipse cx="20" cy="11" rx="12" ry="4.5" fill="currentColor" fill-opacity=".4"/><path d="M8 11 L8 22 Q8 27 20 27 Q32 27 32 22 L32 11" stroke="currentColor" stroke-width="1.5" fill="currentColor" fill-opacity=".15"/><ellipse cx="20" cy="22" rx="12" ry="4.5" fill="currentColor" fill-opacity=".3"/><path d="M8 22 L8 30 Q8 35 20 35 Q32 35 32 30 L32 22" stroke="currentColor" stroke-width="1.5" fill="currentColor" fill-opacity=".12"/><ellipse cx="20" cy="30" rx="12" ry="4.5" fill="currentColor" fill-opacity=".22"/></svg>',

      'Sistemas' => '<svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" rx="10" fill="currentColor" fill-opacity=".12"/><rect x="10" y="10" width="20" height="20" rx="4" fill="currentColor" fill-opacity=".15" stroke="currentColor" stroke-width="1.6"/><rect x="15" y="15" width="10" height="10" rx="2" fill="currentColor" fill-opacity=".4"/><line x1="10" y1="16" x2="5" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".55"/><line x1="10" y1="24" x2="5" y2="24" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".55"/><line x1="30" y1="16" x2="35" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".55"/><line x1="30" y1="24" x2="35" y2="24" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".55"/><line x1="16" y1="10" x2="16" y2="5" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".55"/><line x1="24" y1="10" x2="24" y2="5" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".55"/><line x1="16" y1="30" x2="16" y2="35" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".55"/><line x1="24" y1="30" x2="24" y2="35" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".55"/></svg>',

      'Ingeniería' => '<svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" rx="10" fill="currentColor" fill-opacity=".12"/><circle cx="20" cy="20" r="7" fill="none" stroke="currentColor" stroke-width="2" opacity=".7"/><circle cx="20" cy="20" r="2.5" fill="currentColor" fill-opacity=".7"/><path d="M20 6 L21.5 11 L18.5 11 Z" fill="currentColor" fill-opacity=".5"/><path d="M20 34 L21.5 29 L18.5 29 Z" fill="currentColor" fill-opacity=".5"/><path d="M6 20 L11 18.5 L11 21.5 Z" fill="currentColor" fill-opacity=".5"/><path d="M34 20 L29 18.5 L29 21.5 Z" fill="currentColor" fill-opacity=".5"/><path d="M10.1 10.1 L13.9 14.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" opacity=".4"/><path d="M29.9 10.1 L26.1 14.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" opacity=".4"/><path d="M10.1 29.9 L13.9 25.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" opacity=".4"/><path d="M29.9 29.9 L26.1 25.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" opacity=".4"/></svg>',

      'Redes' => '<svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" rx="10" fill="currentColor" fill-opacity=".12"/><path d="M5 26 Q5 10 20 10 Q35 10 35 26" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" opacity=".5"/><path d="M10 26 Q10 15 20 15 Q30 15 30 26" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" opacity=".6"/><path d="M15 26 Q15 20 20 20 Q25 20 25 26" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" opacity=".75"/><circle cx="20" cy="28" r="3" fill="currentColor" fill-opacity=".7"/><line x1="20" y1="31" x2="20" y2="36" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".5"/><line x1="14" y1="36" x2="26" y2="36" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".5"/></svg>',

      'IA' => '<svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" rx="10" fill="currentColor" fill-opacity=".12"/><circle cx="20" cy="20" r="5" fill="currentColor" fill-opacity=".5"/><circle cx="7" cy="14" r="2.8" fill="currentColor" fill-opacity=".4"/><circle cx="7" cy="26" r="2.8" fill="currentColor" fill-opacity=".4"/><circle cx="20" cy="6" r="2.8" fill="currentColor" fill-opacity=".4"/><circle cx="20" cy="34" r="2.8" fill="currentColor" fill-opacity=".4"/><circle cx="33" cy="14" r="2.8" fill="currentColor" fill-opacity=".4"/><circle cx="33" cy="26" r="2.8" fill="currentColor" fill-opacity=".4"/><line x1="15" y1="17" x2="9.8" y2="15.8" stroke="currentColor" stroke-width="1.4" opacity=".5"/><line x1="15" y1="23" x2="9.8" y2="24.2" stroke="currentColor" stroke-width="1.4" opacity=".5"/><line x1="20" y1="15" x2="20" y2="8.8" stroke="currentColor" stroke-width="1.4" opacity=".5"/><line x1="20" y1="25" x2="20" y2="31.2" stroke="currentColor" stroke-width="1.4" opacity=".5"/><line x1="25" y1="17" x2="30.2" y2="15.8" stroke="currentColor" stroke-width="1.4" opacity=".5"/><line x1="25" y1="23" x2="30.2" y2="24.2" stroke="currentColor" stroke-width="1.4" opacity=".5"/></svg>',
    ];

    foreach ($modules as $i => $module):
      $mid      = (int)$module['id_modulo'];
      $grad     = $gradients[$i % count($gradients)];
      $icon     = $icons[$i % count($icons)];
      $pData    = $progressData[$mid];
      $isDone   = $pData['percent'] === 100;
      $unlocked = $pData['unlocked'] ?? true;
      $isFirst  = $i === 0;
      $categoria = $module['categoria'] ?? '';
      $svgIlus  = $moduleSvgs[$categoria] ?? $defaultSvg;
    ?>

    <div class="module-card <?= !$unlocked ? 'module-card--locked' : '' ?> <?= $isDone ? 'module-card--done' : '' ?>">

      <?php if ($isDone): ?>
        <span class="module-card__done-badge">✅</span>
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

      <!-- Ilustración decorativa del módulo -->
      <div class="module-card__illustration module-card__illustration--<?= $grad ?>">
        <?= $svgIlus ?>
      </div>

      <div class="module-card__body">
        <div class="module-card__top">
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
