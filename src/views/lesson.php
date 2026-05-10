<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($lesson['title']) ?> — Loopbook</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><defs><linearGradient id=%22g%22 x1=%220%22 y1=%220%22 x2=%2240%22 y2=%2240%22 gradientUnits=%22userSpaceOnUse%22><stop offset=%220%25%22 stop-color=%22%23cc0000%22/><stop offset=%2255%25%22 stop-color=%22%23ff2800%22/><stop offset=%22100%25%22 stop-color=%22%23ff6b00%22/></linearGradient></defs><rect width=%2240%22 height=%2240%22 rx=%2211%22 fill=%22url(%23g)%22/><text x=%2250%25%22 y=%2257%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2213%22 font-weight=%22700%22 fill=%22white%22>%3C/%3E</text></svg>">
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<main class="main-content">
<div class="page-container page-container--narrow">

  <a href="<?= base_url('index.php?page=module&id=' . $module['id_modulo']) ?>" class="btn btn-outline btn-back">← Volver a Lecciones</a>

  <div class="lesson-header">
    <div class="lesson-header__top">
      <h1 class="page-title gradient-text"><?= e($lesson['title']) ?></h1>
      <?php if ($completed): ?>
        <span class="badge badge-green">✅ Completada</span>
      <?php endif; ?>
    </div>
    <p class="page-subtitle">📖 <?= e($module['nombre']) ?></p>
  </div>

  <div class="card mb-4">
    <div class="card-stripe card-stripe--purple"></div>
    <div class="card-body">
      <div class="card-section-title">
        <span class="card-section-icon card-section-icon--purple">📖</span>
        <h3>Contenido de la Lección</h3>
      </div>
      <p class="lesson-content"><?= e($lesson['content']) ?></p>
    </div>
  </div>

  <?php if (!empty($exercises)): ?>
  <div class="card mb-4">
    <div class="card-stripe card-stripe--orange"></div>
    <div class="card-body">
      <div class="card-section-title">
        <span class="card-section-icon card-section-icon--orange">✨</span>
        <div>
          <h3>Ejercicios Interactivos</h3>
          <p class="text-sm text-gray">Selecciona la respuesta correcta para cada pregunta</p>
        </div>
      </div>

      <div class="exercises-list">
        <?php foreach ($exercises as $exIdx => $exercise):
          $alreadyDone = $exerciseStatus[$exercise['id']] ?? false;
        ?>
        <div class="exercise <?= $alreadyDone ? 'exercise--done' : '' ?>"
             id="exercise-<?= $exercise['id'] ?>"
             data-exercise-id="<?= $exercise['id'] ?>"
             data-correct="<?= (int)$exercise['correct_answer'] ?>"
             data-correct-option-id="<?= (int)$exercise['correct_option_id'] ?>">

          <div class="exercise__question">
            <span class="exercise__num"><?= $exIdx + 1 ?></span>
            <?= e($exercise['question']) ?>
            <?php if ($alreadyDone): ?>
              <span class="badge badge-green" style="margin-left:auto;font-size:.75rem">✅ Respondido</span>
            <?php endif; ?>
          </div>

          <div class="exercise__options">
            <?php foreach ($exercise['options'] as $optIdx => $option): ?>
            <button type="button"
                    class="option-btn <?= $alreadyDone ? 'option-btn--locked' : '' ?>"
                    data-option="<?= $optIdx ?>"
                    data-option-id="<?= (int)$exercise['option_ids'][$optIdx] ?>"
                    <?= $alreadyDone ? 'disabled' : '' ?>
                    onclick="selectOption(this)">
              <?= e($option) ?>
            </button>
            <?php endforeach; ?>
          </div>

          <?php if (!$alreadyDone): ?>
          <button type="button"
                  class="btn btn-primary btn-full mt-2 check-btn hidden"
                  onclick="checkAnswer(this, <?= $exercise['id'] ?>)">
            Verificar Respuesta
          </button>
          <?php endif; ?>

          <div class="exercise__feedback hidden"></div>
          <p class="exercise__explanation hidden text-sm mt-2"><?= e($exercise['explanation']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div class="lesson-nav">
    <?php if ($prevLesson): ?>
      <a href="<?= base_url('index.php?page=lesson&module_id=' . $module['id_modulo'] . '&lesson_id=' . $prevLesson['id']) ?>"
         class="btn btn-outline">← Anterior</a>
    <?php else: ?>
      <span></span>
    <?php endif; ?>

    <span class="lesson-nav__counter">Lección <?= $lessonNum ?> de <?= $totalCount ?></span>

    <?php if ($nextLesson): ?>
      <a href="<?= base_url('index.php?page=lesson&module_id=' . $module['id_modulo'] . '&lesson_id=' . $nextLesson['id']) ?>"
         class="btn btn-gradient btn-gradient--purple-pink">Siguiente →</a>
    <?php else: ?>
      <a href="<?= base_url('index.php?page=module&id=' . $module['id_modulo']) ?>"
         class="btn btn-gradient btn-gradient--purple-pink">Finalizar →</a>
    <?php endif; ?>
  </div>

  <?php if (!$nextLesson):
    $titleText = '¡Has terminado este módulo!';
    require __DIR__ . '/partials/next_module_card.php';
  endif; ?>

  <?php if (!empty($exercises)): ?>
  <div class="lesson-retry" style="text-align:center; margin-top:1rem;">
    <button type="button"
            class="btn btn-outline"
            onclick="resetLesson()"
            title="Borra tu progreso en esta lección y vuelve a intentarla">
      🔄 Intentar lección de nuevo
    </button>
  </div>
  <?php endif; ?>

</div>
</main>

<script>
  const TOTAL_EXERCISES    = <?= count($exercises) ?>;
  const CHECK_ANSWER_URL   = '<?= base_url('api/check_answer.php') ?>';
  const RESET_PROGRESS_URL = '<?= base_url('api/reset_progress.php') ?>';
  const MODULE_URL         = '<?= base_url('index.php?page=module&id=' . $module['id_modulo']) ?>';
  const MODULE_NAME        = '<?= e($module['nombre']) ?>';
  const CURRENT_LESSON_ID  = <?= (int)$lesson['id'] ?>;
  const CURRENT_MODULE_ID  = <?= (int)$module['id_modulo'] ?>;
  const IS_LAST_LESSON     = <?= $nextLesson ? 'false' : 'true' ?>;

  function resetLesson() {
    if (!confirm('¿Seguro que quieres intentar esta lección de nuevo? Se borrará tu progreso en los ejercicios.')) return;
    const formData = new FormData();
    formData.append('type',      'lesson');
    formData.append('lesson_id', CURRENT_LESSON_ID);
    formData.append('module_id', CURRENT_MODULE_ID);
    fetch(RESET_PROGRESS_URL, { method: 'POST', body: formData })
      .then(r => r.json())
      .then(data => {
        if (data.success) location.reload();
        else alert('No se pudo reiniciar. Intenta de nuevo.');
      })
      .catch(() => alert('Error de conexión.'));
  }
</script>
<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
