<?php
$isEdit = isset($exerciseRow) && $exerciseRow !== null;
$title  = $isEdit ? 'Editar Ejercicio' : 'Nuevo Ejercicio';
// Número de opciones a mostrar (mínimo 4, o las que ya existen)
$numOpts = max(4, count($optionRows ?? []));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?> — Admin Loopbook</title>
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/modules/admin.css') ?>">
</head>
<body>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="main-content">
<div class="page-container page-container--narrow">

  <div class="admin-page-header">
    <div>
      <a href="<?= base_url('index.php?page=admin') ?>" class="admin-breadcrumb">⚙️ Admin</a>
      <span class="admin-breadcrumb-sep">›</span>
      <a href="<?= base_url('index.php?page=admin&action=modules') ?>" class="admin-breadcrumb">Módulos</a>
      <span class="admin-breadcrumb-sep">›</span>
      <a href="<?= base_url('index.php?page=admin&action=lessons&module_id=' . $moduleId) ?>" class="admin-breadcrumb">Lecciones</a>
      <span class="admin-breadcrumb-sep">›</span>
      <a href="<?= base_url('index.php?page=admin&action=exercises&lesson_id=' . $lessonId) ?>" class="admin-breadcrumb">Ejercicios</a>
      <span class="admin-breadcrumb-sep">›</span>
      <span class="admin-breadcrumb"><?= $title ?></span>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error mb-4"><?= e($error) ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-stripe card-stripe--orange"></div>
    <div class="card-body">
      <h2 class="admin-form-title"><?= $isEdit ? '✏️' : '➕' ?> <?= $title ?></h2>

      <form method="POST" class="admin-form" id="exercise-form">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <input type="hidden" name="lesson_id"  value="<?= (int)$lessonId ?>">

        <div class="form-group">
          <label for="pregunta">Pregunta <span class="required">*</span></label>
          <textarea id="pregunta" name="pregunta" class="admin-textarea" rows="3"
                    placeholder="Escribe la pregunta del ejercicio…" required><?= e($isEdit ? $exerciseRow['pregunta'] : '') ?></textarea>
        </div>

        <div class="form-group">
          <label for="tipo">Tipo de ejercicio</label>
          <select id="tipo" name="tipo" class="admin-select">
            <option value="opcion_multiple" <?= (!$isEdit || $exerciseRow['tipo'] === 'opcion_multiple') ? 'selected' : '' ?>>🔘 Opción múltiple</option>
            <option value="verdadero_falso" <?= ($isEdit && $exerciseRow['tipo'] === 'verdadero_falso') ? 'selected' : '' ?>>✅ Verdadero / Falso</option>
          </select>
        </div>

        <div class="form-group">
          <label for="retroalimentacion">Retroalimentación general</label>
          <textarea id="retroalimentacion" name="retroalimentacion" class="admin-textarea" rows="2"
                    placeholder="Explicación que se muestra al responder correctamente…"><?= e($isEdit ? ($exerciseRow['retroalimentacion'] ?? '') : '') ?></textarea>
          <small class="form-hint">Esta explicación aparece debajo de la respuesta correcta para reforzar el aprendizaje.</small>
        </div>

        <!-- Opciones de respuesta -->
        <div class="admin-options-section">
          <div class="admin-options-section__header">
            <h3>Opciones de respuesta</h3>
            <small>Marca ✅ la opción correcta. Puedes agregar hasta 6 opciones.</small>
          </div>

          <div id="options-container">
            <?php for ($i = 0; $i < $numOpts; $i++):
              $opt = $optionRows[$i] ?? null;
            ?>
            <div class="admin-option-row" id="opt-row-<?= $i ?>">
              <div class="admin-option-row__check">
                <input type="checkbox" name="opt_correcta[<?= $i ?>]" id="opt_c_<?= $i ?>"
                       <?= ($opt && $opt['es_correcta']) ? 'checked' : '' ?>>
                <label for="opt_c_<?= $i ?>" title="Marcar como correcta">✅</label>
              </div>
              <div class="admin-option-row__fields">
                <input type="text" name="opt_texto[<?= $i ?>]" class="admin-input"
                       value="<?= e($opt ? $opt['texto'] : '') ?>"
                       placeholder="Texto de la opción <?= $i + 1 ?>">
                <input type="text" name="opt_retro[<?= $i ?>]" class="admin-input admin-input--retro"
                       value="<?= e($opt ? ($opt['retroalimentacion'] ?? '') : '') ?>"
                       placeholder="Retroalimentación para esta opción (opcional)">
              </div>
              <?php if ($i >= 4): ?>
              <button type="button" class="btn btn-danger btn-xs admin-option-row__remove"
                      onclick="removeOption(<?= $i ?>)">✕</button>
              <?php endif; ?>
            </div>
            <?php endfor; ?>
          </div>

          <button type="button" class="btn btn-outline btn-sm mt-2" onclick="addOption()" id="add-opt-btn">
            ➕ Agregar opción
          </button>
        </div>

        <div class="admin-form-actions">
          <a href="<?= base_url('index.php?page=admin&action=exercises&lesson_id=' . $lessonId) ?>" class="btn btn-outline">Cancelar</a>
          <button type="submit" class="btn btn-primary">
            <?= $isEdit ? '💾 Guardar cambios' : '➕ Crear ejercicio' ?>
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
</main>
<script src="<?= base_url('js/app.js') ?>"></script>
<script>
let optCount = <?= $numOpts ?>;
const MAX_OPTS = 6;

function addOption() {
  if (optCount >= MAX_OPTS) {
    document.getElementById('add-opt-btn').disabled = true;
    return;
  }
  const i = optCount;
  const container = document.getElementById('options-container');
  const row = document.createElement('div');
  row.className = 'admin-option-row';
  row.id = 'opt-row-' + i;
  row.innerHTML = `
    <div class="admin-option-row__check">
      <input type="checkbox" name="opt_correcta[${i}]" id="opt_c_${i}">
      <label for="opt_c_${i}" title="Marcar como correcta">✅</label>
    </div>
    <div class="admin-option-row__fields">
      <input type="text" name="opt_texto[${i}]" class="admin-input" placeholder="Texto de la opción ${i+1}">
      <input type="text" name="opt_retro[${i}]" class="admin-input admin-input--retro" placeholder="Retroalimentación (opcional)">
    </div>
    <button type="button" class="btn btn-danger btn-xs admin-option-row__remove" onclick="removeOption(${i})">✕</button>
  `;
  container.appendChild(row);
  optCount++;
  if (optCount >= MAX_OPTS) document.getElementById('add-opt-btn').disabled = true;
}

function removeOption(i) {
  const row = document.getElementById('opt-row-' + i);
  if (row) row.remove();
  if (optCount < MAX_OPTS) document.getElementById('add-opt-btn').disabled = false;
}
</script>
</body>
</html>
