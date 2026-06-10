<?php
$isEdit      = isset($exerciseRow) && $exerciseRow !== null;
$title       = $isEdit ? 'Editar Ejercicio' : 'Nuevo Ejercicio';
$currentTipo = $isEdit ? ($exerciseRow['tipo'] ?? 'opcion_multiple') : 'opcion_multiple';
$numOpts     = ($currentTipo === 'opcion_multiple') ? max(4, count($optionRows ?? [])) : 4;

// Para V/F: detectar cuál opción es la correcta y sus textos
$vfCorrectaIndex = 0;
$vfOpts = [
    0 => ['texto' => 'Verdadero', 'retro' => ''],
    1 => ['texto' => 'Falso',     'retro' => ''],
];
if ($currentTipo === 'verdadero_falso' && !empty($optionRows)) {
    foreach ($optionRows as $idx => $opt) {
        if ((int)$opt['es_correcta'] === 1) $vfCorrectaIndex = $idx;
        if (isset($vfOpts[$idx])) {
            $vfOpts[$idx]['texto'] = $opt['texto'];
            $vfOpts[$idx]['retro'] = $opt['retroalimentacion'] ?? '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?> — Admin Loopbook</title>
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
  <style>
    /* sin estilos extra — se usa el sistema de admin-option-row ya existente */
  </style>
</head>
<body>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="main-content">
<div class="page-container page-container--narrow">

  <!-- Breadcrumb -->
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
      <h2 class="admin-form-title"><?= $isEdit ? 'Editar' : 'Nuevo' ?> <?= $title ?></h2>

      <form method="POST" class="admin-form" id="exercise-form">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <input type="hidden" name="lesson_id"  value="<?= (int)$lessonId ?>">

        <!-- ── Pregunta ── -->
        <div class="form-group">
          <label for="pregunta">Pregunta / Enunciado <span class="required">*</span></label>
          <textarea id="pregunta" name="pregunta" class="admin-textarea" rows="3"
                    placeholder="Escribe aquí el texto de la pregunta o afirmación…"
                    required><?= e($isEdit ? $exerciseRow['pregunta'] : '') ?></textarea>
        </div>

        <!-- ── Tipo ── -->
        <div class="form-group">
          <label for="tipo">Tipo de ejercicio</label>
          <select id="tipo" name="tipo" class="admin-select" onchange="toggleTipo(this.value)">
            <option value="opcion_multiple" <?= $currentTipo === 'opcion_multiple' ? 'selected' : '' ?>>Opcion multiple</option>
            <option value="verdadero_falso" <?= $currentTipo === 'verdadero_falso' ? 'selected' : '' ?>>Verdadero / Falso</option>
            <option value="codigo"          <?= $currentTipo === 'codigo'          ? 'selected' : '' ?>>Escritura de codigo</option>
          </select>
        </div>

        <!-- ── Retroalimentación general ── -->
        <div class="form-group">
          <label for="retroalimentacion">Retroalimentación general</label>
          <textarea id="retroalimentacion" name="retroalimentacion" class="admin-textarea" rows="2"
                    placeholder="Explicación que refuerza el aprendizaje al responder correctamente…"><?= e($isEdit ? ($exerciseRow['retroalimentacion'] ?? '') : '') ?></textarea>
          <small class="form-hint">Se muestra debajo de la respuesta correcta.</small>
        </div>

        <!-- ══════════════════════════════════════════════════
             SECCIÓN A: VERDADERO / FALSO
             Pregunta arriba (ya está en el campo "Pregunta")
             Dos opciones fijas con retroalimentación debajo
        ══════════════════════════════════════════════════════ -->
        <div id="section-vf" style="<?= $currentTipo !== 'verdadero_falso' ? 'display:none' : '' ?>">
          <div class="admin-options-section">
            <div class="admin-options-section__header">
              <h3>Opciones de respuesta</h3>
              <small>Marcar cual de las dos opciones es la respuesta correcta.</small>
            </div>

            <!-- Opción 1: VERDADERO -->
            <div style="background:#fff;border:1.5px solid var(--gray-200);border-radius:10px;
                        padding:1rem 1.1rem;margin-bottom:.75rem;">
              <div style="display:flex;align-items:center;gap:.65rem;margin-bottom:.65rem">
                <input type="checkbox" id="vf_c_0" onclick="onVFCheck(0)"
                       style="width:18px;height:18px;accent-color:#10b981;cursor:pointer;flex-shrink:0"
                       <?= ($currentTipo !== 'verdadero_falso' || $vfCorrectaIndex === 0) ? 'checked' : '' ?>>
                <label for="vf_c_0"
                       style="font-size:1rem;font-weight:700;cursor:pointer;margin:0;
                              color:#059669;display:flex;align-items:center;gap:.4rem">
                  <span style="display:inline-block;width:20px;height:20px;border-radius:50%;
                                background:#059669;color:#fff;font-size:.75rem;font-weight:900;
                                text-align:center;line-height:20px;flex-shrink:0">V</span>
                  Verdadero
                </label>
              </div>
              <div>
                <label style="font-size:.78rem;font-weight:600;color:var(--gray-600);
                               display:block;margin-bottom:.3rem">
                  Retroalimentación para esta opción (opcional)
                </label>
                <input type="text" name="vf_retro_0" class="admin-input"
                       value="<?= e($vfOpts[0]['retro']) ?>"
                       style="width:100%"
                       placeholder="Ej: Correcto. Esta afirmacion es verdadera porque...">
              </div>
              <input type="hidden" name="vf_texto_0" value="Verdadero">
            </div>

            <!-- Opción 2: FALSO -->
            <div style="background:#fff;border:1.5px solid var(--gray-200);border-radius:10px;
                        padding:1rem 1.1rem;margin-bottom:.75rem;">
              <div style="display:flex;align-items:center;gap:.65rem;margin-bottom:.65rem">
                <input type="checkbox" id="vf_c_1" onclick="onVFCheck(1)"
                       style="width:18px;height:18px;accent-color:#10b981;cursor:pointer;flex-shrink:0"
                       <?= ($currentTipo === 'verdadero_falso' && $vfCorrectaIndex === 1) ? 'checked' : '' ?>>
                <label for="vf_c_1"
                       style="font-size:1rem;font-weight:700;cursor:pointer;margin:0;
                              color:#dc2626;display:flex;align-items:center;gap:.4rem">
                  <span style="display:inline-block;width:20px;height:20px;border-radius:50%;
                                background:#dc2626;color:#fff;font-size:.75rem;font-weight:900;
                                text-align:center;line-height:20px;flex-shrink:0">F</span>
                  Falso
                </label>
              </div>
              <div>
                <label style="font-size:.78rem;font-weight:600;color:var(--gray-600);
                               display:block;margin-bottom:.3rem">
                  Retroalimentación para esta opción (opcional)
                </label>
                <input type="text" name="vf_retro_1" class="admin-input"
                       value="<?= e($vfOpts[1]['retro']) ?>"
                       style="width:100%"
                       placeholder="Ej: Incorrecto. Esta afirmacion es falsa porque...">
              </div>
              <input type="hidden" name="vf_texto_1" value="Falso">
            </div>

            <input type="hidden" name="vf_correcta" id="vf_correcta_val"
                   value="<?= $vfCorrectaIndex ?>">

            <small class="form-hint">
              El estudiante vera dos botones: <strong>Verdadero</strong> y <strong>Falso</strong>.
              Tu decides cual es correcto marcando el checkbox de arriba.
            </small>
          </div>
        </div>

        <!-- ══════════════════════════════════════════════════
             SECCIÓN B: OPCIÓN MÚLTIPLE
        ══════════════════════════════════════════════════════ -->
        <div id="section-multiple"
             class="admin-options-section"
             style="<?= $currentTipo === 'verdadero_falso' || $currentTipo === 'codigo' ? 'display:none' : '' ?>">
          <div class="admin-options-section__header">
            <h3>Opciones de respuesta</h3>
            <small>Marca ✅ la opción correcta. Puedes agregar hasta 6 opciones.</small>
          </div>

          <div id="options-container">
            <?php for ($i = 0; $i < $numOpts; $i++):
              $opt = ($currentTipo === 'opcion_multiple') ? ($optionRows[$i] ?? null) : null;
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

        <!-- ══════════════════════════════════════════════════
             SECCIÓN C: ESCRITURA DE CÓDIGO
        ══════════════════════════════════════════════════════ -->
        <div id="section-codigo"
             style="<?= $currentTipo !== 'codigo' ? 'display:none' : '' ?>">
          <div class="admin-options-section">
            <div class="admin-options-section__header">
              <h3>🖥️ Configuración del ejercicio de código</h3>
            </div>

            <div class="form-group">
              <label for="code_instructions">Instrucciones para el alumno</label>
              <textarea id="code_instructions" name="code_instructions" class="admin-textarea" rows="3"
                        placeholder="Ej: Declara una variable llamada 'nombre' con el valor 'Loopbook' e imprímela."><?= e($isEdit ? ($exerciseRow['code_instructions'] ?? '') : '') ?></textarea>
            </div>

            <div class="form-group">
              <label for="expected_output">Salida esperada (exacta)</label>
              <input type="text" id="expected_output" name="expected_output" class="admin-input"
                     value="<?= e($isEdit ? ($exerciseRow['expected_output'] ?? '') : '') ?>"
                     placeholder="Ej: Loopbook">
              <small class="form-hint">Lo que debe imprimir console.log(). Debe coincidir exactamente.</small>
            </div>

            <div class="form-group">
              <label for="code_hint">Pista / código de ejemplo (opcional)</label>
              <textarea id="code_hint" name="code_hint" class="admin-textarea" rows="3"
                        placeholder="Ej: let nombre = 'Loopbook';\nconsole.log(nombre);"><?= e($isEdit ? ($exerciseRow['code_hint'] ?? '') : '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Acciones -->
        <div class="admin-form-actions">
          <a href="<?= base_url('index.php?page=admin&action=exercises&lesson_id=' . $lessonId) ?>"
             class="btn btn-outline">Cancelar</a>
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

// Cambia la sección visible según el tipo seleccionado
function toggleTipo(tipo) {
  document.getElementById('section-vf').style.display       = tipo === 'verdadero_falso' ? '' : 'none';
  document.getElementById('section-multiple').style.display = tipo === 'opcion_multiple'  ? '' : 'none';
  document.getElementById('section-codigo').style.display   = tipo === 'codigo'           ? '' : 'none';
}

// Verdadero/Falso: checkboxes mutuamente exclusivos (solo uno puede estar marcado)
function onVFCheck(idx) {
  const other = idx === 0 ? 1 : 0;
  document.getElementById('vf_c_' + other).checked = false;
  // Actualizar el campo oculto con cuál es la correcta
  document.getElementById('vf_correcta_val').value = idx;
}

// Sincronizar radio directamente (por si acaso)
document.addEventListener('DOMContentLoaded', function() {
  [0, 1].forEach(i => {
    const cb = document.getElementById('vf_c_' + i);
    if (cb) cb.addEventListener('change', () => {
      if (cb.checked) onVFCheck(i);
    });
  });
});

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
