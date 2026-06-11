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

      <?php
        // Mostrar video embebido si la lección tiene URL de video
        $stmt = getDB()->prepare('SELECT tipo, url FROM contenido WHERE id_contenido = ? LIMIT 1');
        $stmt->execute([$lesson['id']]);
        $rawLesson = $stmt->fetch();
      ?>
      <?php if ($rawLesson && $rawLesson['tipo'] === 'video' && !empty($rawLesson['url'])): ?>
      <div class="lesson-video-wrap mt-4">
        <div class="lesson-video-label">🎬 Video de la lección</div>
        <div class="lesson-video-container">
          <iframe src="<?= e($rawLesson['url']) ?>"
                  title="Video de la lección"
                  frameborder="0"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowfullscreen
                  loading="lazy"></iframe>
        </div>
      </div>
      <?php elseif ($rawLesson && $rawLesson['tipo'] === 'imagen' && !empty($rawLesson['url'])): ?>
      <div class="lesson-image-wrap mt-4">
        <img src="<?= e($rawLesson['url']) ?>" alt="Imagen de la lección" class="lesson-image">
      </div>
      <?php endif; ?>
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
        <?php $isCodigo = ($exercise['tipo'] ?? '') === 'codigo'; ?>
        <div class="exercise <?= $alreadyDone ? 'exercise--done' : '' ?> <?= $isCodigo ? 'exercise--codigo' : '' ?>"
             id="exercise-<?= $exercise['id'] ?>"
             data-exercise-id="<?= $exercise['id'] ?>"
             data-correct="<?= (int)$exercise['correct_answer'] ?>"
             data-correct-option-id="<?= (int)$exercise['correct_option_id'] ?>"
             <?php if ($isCodigo): ?>
             data-expected="<?= e($exercise['expected_output'] ?? '') ?>"
             data-hint="<?= e($exercise['code_hint'] ?? '') ?>"
             <?php endif; ?>>

          <div class="exercise__question">
            <span class="exercise__num"><?= $exIdx + 1 ?></span>
            <?= e($exercise['question']) ?>
            <?php if ($alreadyDone): ?>
              <span class="badge badge-green" style="margin-left:auto;font-size:.75rem">✅ Respondido</span>
            <?php endif; ?>
          </div>

          <?php if ($isCodigo): ?>
          <!-- Ejercicio de escritura de código -->
          <div class="exercise__code-area">
            <?php if (!empty($exercise['code_instructions'])): ?>
            <div class="exercise__code-instructions"><?= nl2br(e($exercise['code_instructions'])) ?></div>
            <?php endif; ?>
            <textarea class="exercise__code-editor"
                      placeholder="Escribe tu código aquí..."
                      spellcheck="false"
                      <?= $alreadyDone ? 'disabled' : '' ?>><?= $alreadyDone ? e($exercise['code_hint'] ?? '') : '' ?></textarea>
            <div class="exercise__code-output" style="display:none"></div>
            <?php if (!empty($exercise['code_hint'])): ?>
            <p class="exercise__code-hint">💡 Pista: <?= e($exercise['code_hint']) ?></p>
            <?php endif; ?>
          </div>
          <?php if (!$alreadyDone): ?>
          <button type="button"
                  class="btn btn-primary btn-full mt-2"
                  onclick="checkCodeAnswer(this, <?= $exercise['id'] ?>)">
            ▶ Ejecutar y Verificar
          </button>
          <?php endif; ?>

          <?php else: ?>
          <!-- Ejercicio de opción múltiple / verdadero-falso -->
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
          <?php endif; ?>

          <div class="exercise__feedback hidden"></div>
          <p class="exercise__explanation hidden text-sm mt-2"><?= e($exercise['explanation']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php
    // Calcular si todos los ejercicios están completados al cargar
    $totalEx    = count($exercises);
    $doneAtLoad = count(array_filter($exerciseStatus, fn($v) => $v === true));
    $allDone    = $totalEx === 0 || $doneAtLoad >= $totalEx;

    // URL y label del botón de avance
    $nextUrl   = $nextLesson
      ? base_url('index.php?page=lesson&module_id=' . $module['id_modulo'] . '&lesson_id=' . $nextLesson['id'])
      : base_url('index.php?page=module&id=' . $module['id_modulo']);
    $nextLabel = $nextLesson ? 'Siguiente' : 'Finalizar';
  ?>

  <div class="lesson-nav">
    <!-- Botón anterior: siempre visible -->
    <?php if ($prevLesson): ?>
      <a href="<?= base_url('index.php?page=lesson&module_id=' . $module['id_modulo'] . '&lesson_id=' . $prevLesson['id']) ?>"
         class="btn btn-outline">← Anterior</a>
    <?php else: ?>
      <span></span>
    <?php endif; ?>

    <span class="lesson-nav__counter">Lección <?= $lessonNum ?> de <?= $totalCount ?></span>

    <?php if ($allDone): ?>
      <!-- Todos completados al cargar — botón activo -->
      <a id="btn-next" href="<?= e($nextUrl) ?>"
         class="btn btn-gradient btn-gradient--purple-pink">
        <?= $nextLabel ?> →
      </a>
    <?php else: ?>
      <!-- Pendiente — bloqueado, JS lo activa al completar todos -->
      <button id="btn-next" class="btn btn-nav-locked" disabled
              title="Responde todos los ejercicios para continuar">
        Completa los ejercicios
      </button>
    <?php endif; ?>
  </div>

  <!-- Banner módulo completado (solo última lección) -->
  <?php if (!$nextLesson): ?>
  <div id="module-complete-banner" class="module-complete-banner"
       style="<?= $moduleCompleted ? '' : 'display:none' ?>">
    <div class="module-complete-banner__stars">&#11088;&#11088;&#11088;</div>
    <div class="module-complete-banner__trophy">&#127942;</div>
    <h2 class="module-complete-banner__title">Módulo Completado</h2>
    <p class="module-complete-banner__sub">
      Terminaste <strong><?= e($module['nombre']) ?></strong>
    </p>
    <?php if ($nextModule): ?>
      <p class="module-complete-banner__next-label">Siguiente módulo desbloqueado:</p>
      <p class="module-complete-banner__next-name"><?= e($nextModule['nombre']) ?></p>
      <a href="<?= base_url('index.php?page=module&id=' . (int)$nextModule['id_modulo']) ?>"
         class="module-complete-banner__btn">
        Ir al siguiente módulo →
      </a>
    <?php else: ?>
      <p class="module-complete-banner__next-label">Has completado todo el curso</p>
      <a href="<?= base_url('index.php?page=dashboard') ?>"
         class="module-complete-banner__btn">
        Ver mis logros →
      </a>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($exercises)): ?>
  <div style="text-align:center; margin-top:1rem;">
    <button type="button" class="btn btn-outline btn-sm"
            onclick="resetLesson()">
      Intentar lección de nuevo
    </button>
  </div>
  <?php endif; ?>

</div>
</main>

<script>
  const TOTAL_EXERCISES    = <?= $totalEx ?>;
  const DONE_AT_LOAD       = <?= $doneAtLoad ?>;
  const IS_LAST_LESSON     = <?= $nextLesson ? 'false' : 'true' ?>;
  const NEXT_URL           = '<?= e($nextUrl) ?>';
  const NEXT_LABEL         = '<?= $nextLabel ?>';
  const CHECK_ANSWER_URL   = '<?= base_url('api/check_answer.php') ?>';
  const RESET_PROGRESS_URL = '<?= base_url('api/reset_progress.php') ?>';
  const MODULE_URL         = '<?= base_url('index.php?page=module&id=' . $module['id_modulo']) ?>';
  const MODULE_NAME        = '<?= e($module['nombre']) ?>';
  const CURRENT_LESSON_ID  = <?= (int)$lesson['id'] ?>;
  const CURRENT_MODULE_ID  = <?= (int)$module['id_modulo'] ?>;

  // Contador de respuestas correctas en esta sesión
  let correctCount = DONE_AT_LOAD;

  /**
   * Llamado desde app.js cada vez que se responde correctamente un ejercicio.
   * Desbloquea el botón cuando todos están completos.
   */
  function onExerciseCompleted() {
    correctCount++;
    if (correctCount >= TOTAL_EXERCISES) {
      unlockNav();
    }
  }

  function unlockNav() {
    const btn = document.getElementById('btn-next');
    if (!btn || btn.tagName === 'A') return; // ya es enlace activo
    const link = document.createElement('a');
    link.id        = 'btn-next';
    link.href      = NEXT_URL;
    link.className = 'btn btn-gradient btn-gradient--purple-pink btn-next-unlock';
    link.textContent = NEXT_LABEL + ' →';
    btn.replaceWith(link);

    // Mostrar banner si es la última lección
    if (IS_LAST_LESSON) {
      const banner = document.getElementById('module-complete-banner');
      if (banner) {
        banner.style.display = '';
        setTimeout(() => banner.scrollIntoView({ behavior: 'smooth', block: 'center' }), 400);
      }
    }
  }

  function resetLesson() {
    if (!confirm('¿Quieres volver a intentar esta lección?')) return;
    const fd = new FormData();
    fd.append('type',      'lesson');
    fd.append('lesson_id', CURRENT_LESSON_ID);
    fd.append('module_id', CURRENT_MODULE_ID);
    fetch(RESET_PROGRESS_URL, { method: 'POST', body: fd })
      .then(r => r.json())
      .then(d => { if (d.success) location.reload(); else alert('No se pudo reiniciar.'); })
      .catch(() => alert('Error de conexión.'));
  }
</script>
<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
