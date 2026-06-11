<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Loopbook</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><defs><linearGradient id=%22g%22 x1=%220%22 y1=%220%22 x2=%2240%22 y2=%2240%22 gradientUnits=%22userSpaceOnUse%22><stop offset=%220%25%22 stop-color=%22%23cc0000%22/><stop offset=%2255%25%22 stop-color=%22%23ff2800%22/><stop offset=%22100%25%22 stop-color=%22%23ff6b00%22/></linearGradient></defs><rect width=%2240%22 height=%2240%22 rx=%2211%22 fill=%22url(%23g)%22/><text x=%2250%25%22 y=%2257%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2213%22 font-weight=%22700%22 fill=%22white%22>%3C/%3E</text></svg>">
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
  <style>
    /* ── Tarjetas de módulo rediseñadas ───────────────────── */
    .mod-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1.25rem;
    }

    .mod-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 12px rgba(0,0,0,.07);
      overflow: hidden;
      position: relative;
      transition: transform .2s cubic-bezier(.22,.68,0,1.2), box-shadow .2s;
    }
    .mod-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.12); }
    .mod-card--locked { opacity: .65; filter: grayscale(.35); }
    .mod-card--locked:hover { transform: none; box-shadow: 0 2px 12px rgba(0,0,0,.07); }

    /* Barra superior de color */
    .mod-card__bar { height: 5px; }
    .mod-card__bar--0 { background: linear-gradient(90deg,#1e3a8a,#3b82f6); }
    .mod-card__bar--1 { background: linear-gradient(90deg,#3b82f6,#06b6d4); }
    .mod-card__bar--2 { background: linear-gradient(90deg,#059669,#10b981); }
    .mod-card__bar--3 { background: linear-gradient(90deg,#4f46e5,#7c3aed); }
    .mod-card__bar--4 { background: linear-gradient(90deg,#d97706,#f59e0b); }
    .mod-card__bar--5 { background: linear-gradient(90deg,#dc2626,#ef4444); }
    .mod-card__bar--6 { background: linear-gradient(90deg,#0891b2,#06b6d4); }
    .mod-card__bar--7 { background: linear-gradient(90deg,#7c3aed,#a855f7); }

    .mod-card__body { padding: 1.25rem 1.35rem 1.1rem; }

    /* Header: icono + info */
    .mod-card__head { display: flex; gap: .9rem; align-items: flex-start; margin-bottom: 1rem; }

    .mod-card__ico {
      width: 46px; height: 46px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .mod-card__ico--0 { background: linear-gradient(135deg,#1e3a8a,#3b82f6); }
    .mod-card__ico--1 { background: linear-gradient(135deg,#3b82f6,#06b6d4); }
    .mod-card__ico--2 { background: linear-gradient(135deg,#059669,#10b981); }
    .mod-card__ico--3 { background: linear-gradient(135deg,#4f46e5,#7c3aed); }
    .mod-card__ico--4 { background: linear-gradient(135deg,#d97706,#f59e0b); }
    .mod-card__ico--5 { background: linear-gradient(135deg,#dc2626,#ef4444); }
    .mod-card__ico--6 { background: linear-gradient(135deg,#0891b2,#06b6d4); }
    .mod-card__ico--7 { background: linear-gradient(135deg,#7c3aed,#a855f7); }

    .mod-card__meta { flex: 1; min-width: 0; }
    .mod-card__num {
      font-size: .68rem; font-weight: 800; letter-spacing: .8px;
      text-transform: uppercase; color: #9ca3af; display: block; margin-bottom: .15rem;
    }
    .mod-card__title {
      font-size: .95rem; font-weight: 800; color: #111827; line-height: 1.3;
      margin: 0 0 .25rem;
      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .mod-card__desc {
      font-size: .78rem; color: #6b7280; line-height: 1.45;
      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }

    /* Progreso */
    .mod-card__progress { margin-bottom: .9rem; }
    .mod-card__prow {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: .35rem; font-size: .8rem; color: #6b7280;
    }
    .mod-card__prow strong { font-weight: 700; color: #374151; }
    .mod-card__pbar {
      height: 7px; background: #f3f4f6; border-radius: 999px; overflow: hidden;
    }
    .mod-card__pfill {
      height: 100%; border-radius: 999px;
      transition: width .6s cubic-bezier(.22,.68,0,1.2);
    }
    .mod-card__pfill--0 { background: linear-gradient(90deg,#1e3a8a,#3b82f6); }
    .mod-card__pfill--1 { background: linear-gradient(90deg,#3b82f6,#06b6d4); }
    .mod-card__pfill--2 { background: linear-gradient(90deg,#059669,#10b981); }
    .mod-card__pfill--3 { background: linear-gradient(90deg,#4f46e5,#7c3aed); }
    .mod-card__pfill--4 { background: linear-gradient(90deg,#d97706,#f59e0b); }
    .mod-card__pfill--5 { background: linear-gradient(90deg,#dc2626,#ef4444); }
    .mod-card__pfill--6 { background: linear-gradient(90deg,#0891b2,#06b6d4); }
    .mod-card__pfill--7 { background: linear-gradient(90deg,#7c3aed,#a855f7); }

    /* Footer */
    .mod-card__foot {
      display: flex; align-items: center; justify-content: space-between;
      padding-top: .75rem; border-top: 1px solid #f3f4f6;
    }
    .mod-card__ex { font-size: .78rem; color: #9ca3af; font-weight: 600; }

    /* Botones de la card */
    .mod-card__btn {
      display: inline-flex; align-items: center; gap: .35rem;
      padding: .42rem 1rem; border-radius: 8px;
      font-size: .82rem; font-weight: 700;
      text-decoration: none; white-space: nowrap;
      transition: transform .15s, box-shadow .15s;
      color: #fff;
    }
    .mod-card__btn:hover { transform: translateY(-1px); text-decoration: none; color: #fff; }
    .mod-card__btn--0 { background: linear-gradient(135deg,#1e3a8a,#3b82f6); box-shadow: 0 3px 10px rgba(30,58,138,.3); }
    .mod-card__btn--1 { background: linear-gradient(135deg,#3b82f6,#06b6d4); box-shadow: 0 3px 10px rgba(59,130,246,.3); }
    .mod-card__btn--2 { background: linear-gradient(135deg,#059669,#10b981); box-shadow: 0 3px 10px rgba(5,150,105,.3); }
    .mod-card__btn--3 { background: linear-gradient(135deg,#4f46e5,#7c3aed); box-shadow: 0 3px 10px rgba(79,70,229,.3); }
    .mod-card__btn--4 { background: linear-gradient(135deg,#d97706,#f59e0b); box-shadow: 0 3px 10px rgba(217,119,6,.3); }
    .mod-card__btn--5 { background: linear-gradient(135deg,#dc2626,#ef4444); box-shadow: 0 3px 10px rgba(220,38,38,.3); }
    .mod-card__btn--6 { background: linear-gradient(135deg,#0891b2,#06b6d4); box-shadow: 0 3px 10px rgba(8,145,178,.3); }
    .mod-card__btn--7 { background: linear-gradient(135deg,#7c3aed,#a855f7); box-shadow: 0 3px 10px rgba(124,58,237,.3); }

    /* Badge completado */
    .mod-card__done {
      position: absolute; top: .8rem; right: .8rem; z-index: 2;
      background: #10b981; color: #fff; border-radius: 50%;
      width: 24px; height: 24px;
      display: flex; align-items: center; justify-content: center;
      font-size: .8rem; font-weight: 900;
      box-shadow: 0 2px 8px rgba(16,185,129,.4);
    }

    /* Mensaje bloqueado */
    .mod-card__lock-msg {
      font-size: .75rem; color: #9ca3af; font-style: italic;
    }

    /* Dark mode */
    html.dark .mod-card { background: var(--surface); }
    html.dark .mod-card__title { color: var(--text); }
    html.dark .mod-card__prow strong { color: var(--text); }
    html.dark .mod-card__pbar { background: #1e2d4a; }
    html.dark .mod-card__foot { border-color: var(--border); }

    /* Responsive */
    @media(max-width:640px) { .mod-grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<main class="main-content">
<div class="page-container">

  <?php
  $lockMsg = $_SESSION['lock_msg'] ?? $_SESSION['admin_error'] ?? '';
  unset($_SESSION['lock_msg'], $_SESSION['admin_error']);
  ?>
  <?php if ($lockMsg): ?>
  <div class="alert-lock">
    <span style="font-size:1rem">&#128274;</span>
    <span><?= e($lockMsg) ?></span>
  </div>
  <?php endif; ?>

  <!-- Header -->
  <div class="dashboard-header">
    <div>
      <h1 class="page-title gradient-text">
        Hola, <?= e($_SESSION['nombre'] ?? $_SESSION['username'] ?? 'estudiante') ?>!
      </h1>
      <p class="page-subtitle">Completa cada módulo en orden para desbloquear el siguiente</p>
    </div>
    <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
      <?php if (!empty($_SESSION['is_admin'])): ?>
        <a href="<?= base_url('index.php?page=admin') ?>" class="btn btn-outline btn-sm">Panel Admin</a>
      <?php endif; ?>
      <div class="progress-badge">
        <span class="progress-badge__value"><?= $overallProgress ?>%</span>
        <span class="progress-badge__label">Progreso Total</span>
      </div>
    </div>
  </div>

  <!-- Barra global -->
  <div style="height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;margin-bottom:2rem">
    <div style="height:100%;width:<?= $overallProgress ?>%;
                background:linear-gradient(90deg,#4f46e5,#7c3aed);border-radius:999px;
                transition:width .8s cubic-bezier(.22,.68,0,1.2)"></div>
  </div>

  <!-- Grid de módulos -->
  <div class="mod-grid">
    <?php
    $colorIdx = [0,1,2,3,4,5,6,7];

    // SVGs de programación
    $svgIcons = [
      '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
      '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>',
      '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="4" r="2"/><circle cx="4" cy="20" r="2"/><circle cx="20" cy="20" r="2"/><line x1="12" y1="6" x2="12" y2="13"/><line x1="12" y1="13" x2="4" y2="18"/><line x1="12" y1="13" x2="20" y2="18"/></svg>',
      '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>',
      '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>',
      '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M4.93 19.07l1.41-1.41M19.07 19.07l-1.41-1.41M12 2v2M12 20v2M2 12h2M20 12h2"/></svg>',
      '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
      '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/></svg>',
    ];

    foreach ($modules as $i => $module):
      $mid      = (int)$module['id_modulo'];
      $ci       = $i % 8;           // color index
      $svgIcon  = $svgIcons[$ci];
      $pData    = $progressData[$mid];
      $isDone   = $pData['percent'] === 100;
      $unlocked = $pData['unlocked'] ?? true;
    ?>
    <div class="mod-card <?= !$unlocked ? 'mod-card--locked' : '' ?>">

      <?php if ($isDone): ?>
        <div class="mod-card__done">&#10003;</div>
      <?php endif; ?>

      <div class="mod-card__bar mod-card__bar--<?= $ci ?>"></div>

      <div class="mod-card__body">
        <!-- Cabecera -->
        <div class="mod-card__head">
          <div class="mod-card__ico mod-card__ico--<?= $ci ?>">
            <?= $svgIcon ?>
          </div>
          <div class="mod-card__meta">
            <span class="mod-card__num">MODULO <?= $i + 1 ?></span>
            <h3 class="mod-card__title"><?= e($module['nombre']) ?></h3>
            <p class="mod-card__desc"><?= e($module['descripcion'] ?? '') ?></p>
          </div>
        </div>

        <!-- Progreso -->
        <?php if ($unlocked): ?>
        <div class="mod-card__progress">
          <div class="mod-card__prow">
            <span>Ejercicios completados</span>
            <strong><?= $pData['completed'] ?>/<?= $pData['total'] ?> (<?= $pData['percent'] ?>%)</strong>
          </div>
          <div class="mod-card__pbar">
            <div class="mod-card__pfill mod-card__pfill--<?= $ci ?>"
                 style="width:<?= $pData['percent'] ?>%"></div>
          </div>
        </div>
        <?php else: ?>
        <div class="mod-card__progress">
          <div class="mod-card__prow">
            <span style="color:#9ca3af">Pendiente de desbloquear</span>
            <strong style="color:#d1d5db">0/<?= $pData['total'] ?></strong>
          </div>
          <div class="mod-card__pbar">
            <div class="mod-card__pfill" style="width:0;background:#e5e7eb"></div>
          </div>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="mod-card__foot">
          <span class="mod-card__ex"><?= $pData['total'] ?> ejercicios</span>

          <?php if ($unlocked): ?>
            <?php if (!empty($_SESSION['is_admin'])): ?>
              <a href="<?= base_url('index.php?page=admin&action=modules') ?>"
                 class="mod-card__btn mod-card__btn--<?= $ci ?>">
                Editar
              </a>
            <?php else: ?>
              <a href="<?= base_url('index.php?page=module&id=' . $mid) ?>"
                 class="mod-card__btn mod-card__btn--<?= $ci ?>">
                <?php
                  if ($isDone)                    echo 'Repasar';
                  elseif ($pData['percent'] > 0)  echo 'Continuar';
                  else                            echo 'Comenzar';
                ?>
              </a>
            <?php endif; ?>
          <?php else: ?>
            <span class="mod-card__lock-msg">
              Completa el modulo <?= $i ?> primero
            </span>
          <?php endif; ?>
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
