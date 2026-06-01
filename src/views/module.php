<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($module['nombre']) ?> — Loopbook</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><defs><linearGradient id=%22g%22 x1=%220%22 y1=%220%22 x2=%2240%22 y2=%2240%22 gradientUnits=%22userSpaceOnUse%22><stop offset=%220%25%22 stop-color=%22%23cc0000%22/><stop offset=%2255%25%22 stop-color=%22%23ff2800%22/><stop offset=%22100%25%22 stop-color=%22%23ff6b00%22/></linearGradient></defs><rect width=%2240%22 height=%2240%22 rx=%2211%22 fill=%22url(%23g)%22/><text x=%2250%25%22 y=%2257%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2213%22 font-weight=%22700%22 fill=%22white%22>%3C/%3E</text></svg>">
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<main class="main-content">
<div class="page-container page-container--narrow">

  <a href="<?= base_url('index.php?page=dashboard') ?>" class="btn btn-outline btn-back">← Volver a Módulos</a>

  <div class="module-header">
    <h1 class="page-title gradient-text"><?= e($module['nombre']) ?></h1>
    <p class="page-subtitle"><?= e($module['descripcion']) ?></p>

    <div class="card mt-4">
      <div class="card-body">
        <div class="progress-row">
          <span>✨ <strong>Ejercicios completados</strong></span>
          <span class="text-purple font-bold"><?= $completedEx ?> / <?= $totalEx ?></span>
        </div>
        <div class="progress-bar mt-2">
          <div class="progress-bar__fill progress-bar__fill--purple-pink"
               style="width:<?= $progressPct ?>%"></div>
        </div>
        <?php if ($completedEx > 0): ?>
        <div style="text-align:right; margin-top:0.75rem;">
          <button type="button"
                  class="btn btn-outline btn-sm"
                  onclick="resetModule(<?= (int)$module['id_modulo'] ?>)">
            🔄 Reiniciar módulo
          </button>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Lista de lecciones -->
  <div class="lessons-list">
    <?php foreach ($lessons as $i => $lesson): ?>
    <div class="lesson-card <?= $lesson['completed'] ? 'lesson-card--done' : '' ?>">
      <div class="lesson-card__stripe <?= $lesson['completed'] ? 'lesson-card__stripe--green' : 'lesson-card__stripe--purple' ?>"></div>
      <div class="lesson-card__body">
        <div class="lesson-card__icon">
          <?php if ($lesson['completed']): ?>
            <div class="icon-circle icon-circle--green">✅</div>
          <?php else: ?>
            <div class="icon-circle icon-circle--purple">📄</div>
          <?php endif; ?>
        </div>
        <div class="lesson-card__info">
          <span class="lesson-card__num">📖 Lección <?= $i + 1 ?></span>
          <h3 class="lesson-card__title"><?= e($lesson['title']) ?></h3>
          <p class="lesson-card__excerpt"><?= e(mb_substr($lesson['content'], 0, 120)) ?>…</p>
        </div>
        <a href="<?= base_url('index.php?page=lesson&module_id=' . $module['id_modulo'] . '&lesson_id=' . $lesson['id']) ?>"
           class="btn btn-sm btn-gradient btn-gradient--red-orange">
          <?= $lesson['completed'] ? '🔁 Revisar' : '▶️ Comenzar' ?> →
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- ── Banner siguiente módulo: SOLO cuando progressPct = 100 ── -->
  <?php if ($progressPct === 100 && $totalEx > 0): ?>
  <div class="module-complete-banner">
    <div class="module-complete-banner__stars">⭐⭐⭐</div>
    <div class="module-complete-banner__trophy">🏆</div>
    <h2 class="module-complete-banner__title">¡Módulo Completado!</h2>
    <p class="module-complete-banner__sub">
      Terminaste todos los ejercicios de <strong><?= e($module['nombre']) ?></strong>
    </p>
    <?php if ($nextModule): ?>
      <p class="module-complete-banner__next-label">Siguiente módulo desbloqueado:</p>
      <p class="module-complete-banner__next-name">📦 <?= e($nextModule['nombre']) ?></p>
      <a href="<?= base_url('index.php?page=module&id=' . (int)$nextModule['id_modulo']) ?>"
         class="module-complete-banner__btn">
        🚀 Ir al siguiente módulo →
      </a>
    <?php else: ?>
      <p class="module-complete-banner__next-label">🎓 ¡Has completado todo el curso!</p>
      <a href="<?= base_url('index.php?page=dashboard') ?>"
         class="module-complete-banner__btn">
        🏠 Ver mis logros →
      </a>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</div>
</main>
<script src="<?= base_url('js/app.js') ?>"></script>
<script>
  const RESET_PROGRESS_URL = '<?= base_url('api/reset_progress.php') ?>';

  function resetModule(moduleId) {
    if (!confirm('¿Seguro que quieres reiniciar todo el módulo?')) return;
    const fd = new FormData();
    fd.append('type',      'module');
    fd.append('module_id', moduleId);
    fetch(RESET_PROGRESS_URL, { method: 'POST', body: fd })
      .then(r => r.json())
      .then(d => { if (d.success) location.reload(); else alert('No se pudo reiniciar.'); })
      .catch(() => alert('Error de conexión.'));
  }
</script>
</body>
</html>
