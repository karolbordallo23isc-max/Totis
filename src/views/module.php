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
        <?php if ($completed > 0): ?>
        <div style="text-align:right; margin-top:0.75rem;">
          <button type="button"
                  class="btn btn-outline btn-sm"
                  onclick="resetModule(<?= (int)$module['id_modulo'] ?>)"
                  title="Borra todo el progreso de este módulo para volver a intentarlo">
            🔄 Reiniciar módulo
          </button>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

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
        <?php if (!empty($_SESSION['is_admin'])): ?>
          <a href="<?= base_url('index.php?page=admin&action=lesson_edit&id=' . $lesson['id']) ?>"
             class="btn btn-sm btn-gradient btn-gradient--red-orange">
            ✏️ Editar →
          </a>
        <?php else: ?>
          <a href="<?= base_url('index.php?page=lesson&module_id=' . $module['id_modulo'] . '&lesson_id=' . $lesson['id']) ?>"
             class="btn btn-sm btn-gradient btn-gradient--red-orange">
            <?= $lesson['completed'] ? 'Revisar' : 'Comenzar' ?> →
          </a>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <?php if ($progressPct === 100 && $totalEx > 0): ?>
  <div class="next-module-banner">
    <div class="next-module-banner__confetti">🎉</div>
    <div class="next-module-banner__body">
      <div class="next-module-banner__check">✅</div>
      <div class="next-module-banner__text">
        <h3 class="next-module-banner__title">¡Módulo completado!</h3>
        <?php if ($nextModule): ?>
          <p class="next-module-banner__sub">Siguiente: <strong><?= e($nextModule['nombre']) ?></strong></p>
        <?php else: ?>
          <p class="next-module-banner__sub">🏆 ¡Has completado todos los módulos del curso!</p>
        <?php endif; ?>
      </div>
    </div>
    <?php if ($nextModule): ?>
      <a href="<?= base_url('index.php?page=module&id=' . (int)$nextModule['id_modulo']) ?>"
         class="btn next-module-banner__btn">
        Ir al siguiente módulo →
      </a>
    <?php else: ?>
      <a href="<?= base_url('index.php?page=dashboard') ?>"
         class="btn next-module-banner__btn">
        Ver todos los módulos →
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
    if (!confirm('¿Seguro que quieres reiniciar todo el módulo? Se borrará todo tu progreso en este módulo.')) return;
    const formData = new FormData();
    formData.append('type',      'module');
    formData.append('module_id', moduleId);
    fetch(RESET_PROGRESS_URL, { method: 'POST', body: formData })
      .then(r => r.json())
      .then(data => {
        if (data.success) location.reload();
        else alert('No se pudo reiniciar. Intenta de nuevo.');
      })
      .catch(() => alert('Error de conexión.'));
  }
</script>
</body>
</html>
