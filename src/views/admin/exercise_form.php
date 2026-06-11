<?php
$isEdit      = isset($exerciseRow) && $exerciseRow !== null;
$title       = $isEdit ? 'Editar Ejercicio' : 'Nuevo Ejercicio';
$currentTipo = $isEdit ? ($exerciseRow['tipo'] ?? 'opcion_multiple') : 'opcion_multiple';
$numOpts     = ($currentTipo === 'opcion_multiple') ? max(4, count($optionRows ?? [])) : 4;

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
  <link rel="stylesheet" href="<?= base_url('css/modules/admin.css') ?>">
  <style>
    /* ── Opciones rediseñadas sin emojis ─────────────────── */
    .opt-section {
      background: #f8f9ff;
      border: 1.5px solid #e5e7eb;
      border-radius: 12px;
      padding: 1.25rem 1.35rem;
    }
    .opt-section__hdr {
      margin-bottom: 1rem;
    }
    .opt-section__hdr h3 {
      font-size: .95rem; font-weight: 700; color: #111827; margin: 0 0 .2rem;
    }
    .opt-section__hdr small { font-size: .78rem; color: #6b7280; }

    /* Fila de opcion */
    .opt-item {
      display: grid;
      grid-template-columns: 36px 1fr 1fr;
      align-items: center;
      gap: .6rem;
      background: #fff;
      border: 1.5px solid #e5e7eb;
      border-radius: 9px;
      padding: .65rem .85rem;
      margin-bottom: .6rem;
      transition: border-color .15s;
    }
    .opt-item:focus-within { border-color: #7c3aed; }
    .opt-item--removable { grid-template-columns: 36px 1fr 1fr 28px; }

    /* Checkbox personalizado */
    .opt-check {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 2px;
    }
    .opt-check input[type="checkbox"] {
      width: 18px; height: 18px;
      accent-color: #7c3aed;
      cursor: pointer;
    }
    .opt-check-lbl {
      font-size: .6rem; color: #9ca3af; font-weight: 600;
      text-transform: uppercase; letter-spacing: .3px;
      line-height: 1;
    }

    .opt-item input[type="text"] {
      width: 100%; padding: .5rem .7rem;
      border: 1.5px solid #e5e7eb; border-radius: 7px;
      font-size: .88rem; outline: none; background: #fafafa;
      color: #111827; transition: border-color .15s;
      font-family: inherit;
    }
    .opt-item input[type="text"]:focus {
      border-color: #7c3aed; background: #fff;
    }
    .opt-item input[type="text"]::placeholder { color: #9ca3af; }

    .opt-remove {
      background: none; border: none; color: #dc2626;
      cursor: pointer; font-size: .9rem; padding: .2rem .3rem;
      border-radius: 5px; line-height: 1; font-weight: 700;
    }
    .opt-remove:hover { background: #fef2f2; }

    /* V/F */
    .vf-item {
      background: #fff; border: 1.5px solid #e5e7eb;
      border-radius: 9px; padding: .85rem 1rem;
      margin-bottom: .65rem; transition: border-color .15s;
    }
    .vf-item:focus-within { border-color: #7c3aed; }
    .vf-item__top {
      display: flex; align-items: center; gap: .6rem; margin-bottom: .65rem;
    }
    .vf-item__top input[type="checkbox"] {
      width: 18px; height: 18px; accent-color: #7c3aed; cursor: pointer;
    }
    .vf-item__top .vf-dot {
      display: inline-flex; align-items: center; justify-content: center;
      width: 22px; height: 22px; border-radius: 50%;
      font-size: .72rem; font-weight: 900; color: #fff; flex-shrink: 0;
    }
    .vf-item__top label {
      font-size: .95rem; font-weight: 700; cursor: pointer;
      display: flex; align-items: center; gap: .4rem; margin: 0;
    }
    .vf-item__lbl {
      font-size: .75rem; font-weight: 600; color: #6b7280;
      display: block; margin-bottom: .3rem; text-transform: uppercase;
      letter-spacing: .03em;
    }
    .vf-item input[type="text"] {
      width: 100%; padding: .5rem .7rem;
      border: 1.5px solid #e5e7eb; border-radius: 7px;
      font-size: .88rem; outline: none; background: #fafafa;
      font-family: inherit; transition: border-color .15s;
    }
    .vf-item input[type="text"]:focus { border-color: #7c3aed; background: #fff; }

    /* Boton agregar opcion */
    .btn-add-opt {
      display: inline-flex; align-items: center; gap: .4rem;
      margin-top: .75rem; background: #fff;
      border: 1.5px dashed #7c3aed; color: #7c3aed;
      border-radius: 8px; padding: .45rem 1rem;
      font-size: .84rem; font-weight: 700; cursor: pointer;
      transition: background .15s;
    }
    .btn-add-opt:hover { background: #f5f3ff; }

    html.dark .opt-section, html.dark .opt-item, html.dark .vf-item {
      background: var(--surface); border-color: var(--border);
    }
    html.dark .opt-item input[type="text"],
    html.dark .vf-item input[type="text"] {
      background: var(--bg); border-color: var(--border); color: var(--text);
    }
  </style>
</head>
<body>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="main-content">
<div class="page-container page-container--narrow">

  <div class="admin-page-header">
    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
      <a href="<?= base_url('index.php?page=admin') ?>" class="admin-breadcrumb">Admin</a>
      <span class="admin-breadcrumb-sep">›</span>
      <a href="<?= base_url('index.php?page=admin&action=modules') ?>" class="admin-breadcrumb">Modulos</a>
      <span class="admin-breadcrumb-sep">›</span>
      <a href="<?= base_url('index.php?page=admin&action=lessons&module_id=' . $moduleId) ?>" class="admin-breadcrumb">Lecciones</a>
      <span class="admin-breadcrumb-sep">›</span>
      <a href="<?= base_url('index.php?page=admin&action=exercises&lesson_id=' . $lessonId) ?>" class="admin-breadcrumb">Ejercicios</a>
      <span class="admin-breadcrumb-sep">›</span>
      <span style="font-size:.85rem;font-weight:600;color:#111827"><?= $title ?></span>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error mb-4"><?= e($error) ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-stripe card-stripe--orange"></div>
    <div class="card-body">
      <h2 style="font-size:1.4rem;font-weight:900;color:#111827;margin:0 0 1.5rem"><?= $title ?></h2>

      <form method="POST" class="admin-form" id="exercise-form">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <input type="hidden" name="lesson_id"  value="<?= (int)$lessonId ?>">

        <!-- Pregunta -->
        <div class="form-group">
          <label class="mf-label" style="font-size:.8rem;font-weight:700;color:#374151;
                 text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:.4rem">
            Pregunta / Enunciado <span style="color:#7c3aed">*</span>
          </label>
          <textarea name="pregunta" class="admin-textarea" rows="3"
                    placeholder="Escribe el texto de la pregunta o afirmacion..."
                    required><?= e($isEdit ? $exerciseRow['pregunta'] : '') ?></textarea>
        </div>

        <!-- Tipo -->
        <div class="form-group">
          <label style="font-size:.8rem;font-weight:700;color:#374151;
                 text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:.4rem">
            Tipo de ejercicio
          </label>
          <select name="tipo" class="admin-select" onchange="toggleTipo(this.value)">
            <option value="opcion_multiple" <?= $currentTipo === 'opcion_multiple' ? 'selected' : '' ?>>Opcion multiple</option>
            <option value="verdadero_falso" <?= $currentTipo === 'verdadero_falso' ? 'selected' : '' ?>>Verdadero / Falso</option>
            <option value="codigo"          <?= $currentTipo === 'codigo'          ? 'selected' : '' ?>>Escritura de codigo</option>
          </select>
        </div>

        <!-- Retroalimentacion general -->
        <div class="form-group">
          <label style="font-size:.8rem;font-weight:700;color:#374151;
                 text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:.4rem">
            Retroalimentacion general
          </label>
          <textarea name="retroalimentacion" class="admin-textarea" rows="2"
                    placeholder="Explicacion que aparece al responder correctamente..."><?= e($isEdit ? ($exerciseRow['retroalimentacion'] ?? '') : '') ?></textarea>
          <small class="form-hint">Se muestra debajo de la respuesta correcta.</small>
        </div>

        <!-- ── OPCION MULTIPLE ── -->
        <div id="section-multiple"
             style="<?= $currentTipo !== 'opcion_multiple' ? 'display:none' : '' ?>">
          <div class="opt-section">
            <div class="opt-section__hdr">
              <h3>Opciones de respuesta</h3>
              <small>Marca la casilla de la opcion correcta. Maximo 6 opciones.</small>
            </div>

            <div id="options-container">
              <?php for ($i = 0; $i < $numOpts; $i++):
                $opt = ($currentTipo === 'opcion_multiple') ? ($optionRows[$i] ?? null) : null;
              ?>
              <div class="opt-item <?= $i >= 4 ? 'opt-item--removable' : '' ?>" id="opt-row-<?= $i ?>">
                <div class="opt-check">
                  <input type="checkbox" name="opt_correcta[<?= $i ?>]" id="opt_c_<?= $i ?>"
                         <?= ($opt && $opt['es_correcta']) ? 'checked' : '' ?>>
                  <span class="opt-check-lbl">OK</span>
                </div>
                <input type="text" name="opt_texto[<?= $i ?>]"
                       value="<?= e($opt ? $opt['texto'] : '') ?>"
                       placeholder="Texto de la opcion <?= $i + 1 ?>">
                <input type="text" name="opt_retro[<?= $i ?>]"
                       value="<?= e($opt ? ($opt['retroalimentacion'] ?? '') : '') ?>"
                       placeholder="Retroalimentacion (opcional)">
                <?php if ($i >= 4): ?>
                <button type="button" class="opt-remove" onclick="removeOption(<?= $i ?>)" title="Eliminar">x</button>
                <?php endif; ?>
              </div>
              <?php endfor; ?>
            </div>

            <button type="button" class="btn-add-opt" onclick="addOption()" id="add-opt-btn">
              + Agregar opcion
            </button>
          </div>
        </div>

        <!-- ── VERDADERO / FALSO ── -->
        <div id="section-vf"
             style="<?= $currentTipo !== 'verdadero_falso' ? 'display:none' : '' ?>">
          <div class="opt-section">
            <div class="opt-section__hdr">
              <h3>Opciones de respuesta</h3>
              <small>Marca la casilla de la opcion correcta. El estudiante vera dos botones.</small>
            </div>

            <!-- Verdadero -->
            <div class="vf-item">
              <div class="vf-item__top">
                <input type="checkbox" id="vf_c_0" onclick="onVFCheck(0)"
                       <?= ($currentTipo !== 'verdadero_falso' || $vfCorrectaIndex === 0) ? 'checked' : '' ?>>
                <label for="vf_c_0">
                  <span class="vf-dot" style="background:#059669">V</span>
                  Verdadero
                </label>
              </div>
              <span class="vf-item__lbl">Retroalimentacion (opcional)</span>
              <input type="text" name="vf_retro_0"
                     value="<?= e($vfOpts[0]['retro']) ?>"
                     placeholder="Ej: Correcto, esta afirmacion es verdadera...">
              <input type="hidden" name="vf_texto_0" value="Verdadero">
            </div>

            <!-- Falso -->
            <div class="vf-item">
              <div class="vf-item__top">
                <input type="checkbox" id="vf_c_1" onclick="onVFCheck(1)"
                       <?= ($currentTipo === 'verdadero_falso' && $vfCorrectaIndex === 1) ? 'checked' : '' ?>>
                <label for="vf_c_1">
                  <span class="vf-dot" style="background:#dc2626">F</span>
                  Falso
                </label>
              </div>
              <span class="vf-item__lbl">Retroalimentacion (opcional)</span>
              <input type="text" name="vf_retro_1"
                     value="<?= e($vfOpts[1]['retro']) ?>"
                     placeholder="Ej: Incorrecto, esta afirmacion es falsa porque...">
              <input type="hidden" name="vf_texto_1" value="Falso">
            </div>

            <input type="hidden" name="vf_correcta" id="vf_correcta_val" value="<?= $vfCorrectaIndex ?>">
            <small class="form-hint">
              Tu decides cual es correcto marcando la casilla de arriba.
            </small>
          </div>
        </div>

        <!-- ── CODIGO ── -->
        <div id="section-codigo"
             style="<?= $currentTipo !== 'codigo' ? 'display:none' : '' ?>">
          <div class="opt-section">
            <div class="opt-section__hdr">
              <h3>Configuracion del ejercicio de codigo</h3>
            </div>
            <div class="form-group">
              <label style="font-weight:700;font-size:.82rem;color:#374151;display:block;margin-bottom:.35rem">
                Instrucciones para el alumno
              </label>
              <textarea name="code_instructions" class="admin-textarea" rows="3"
                        placeholder="Ej: Declara una variable llamada nombre con el valor Loopbook e imprimela."><?= e($isEdit ? ($exerciseRow['code_instructions'] ?? '') : '') ?></textarea>
            </div>
            <div class="form-group">
              <label style="font-weight:700;font-size:.82rem;color:#374151;display:block;margin-bottom:.35rem">
                Salida esperada (exacta)
              </label>
              <input type="text" name="expected_output" class="admin-input"
                     value="<?= e($isEdit ? ($exerciseRow['expected_output'] ?? '') : '') ?>"
                     placeholder="Ej: Loopbook">
              <small class="form-hint">Lo que debe imprimir console.log(). Debe coincidir exactamente.</small>
            </div>
            <div class="form-group">
              <label style="font-weight:700;font-size:.82rem;color:#374151;display:block;margin-bottom:.35rem">
                Pista o codigo de ejemplo (opcional)
              </label>
              <textarea name="code_hint" class="admin-textarea" rows="3"
                        placeholder="Ej: let nombre = 'Loopbook'; console.log(nombre);"><?= e($isEdit ? ($exerciseRow['code_hint'] ?? '') : '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Acciones -->
        <div class="admin-form-actions">
          <a href="<?= base_url('index.php?page=admin&action=exercises&lesson_id=' . $lessonId) ?>"
             class="btn btn-outline">Cancelar</a>
          <button type="submit" class="btn btn-primary">
            <?= $isEdit ? 'Guardar cambios' : '+ Crear ejercicio' ?>
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

function toggleTipo(tipo) {
  document.getElementById('section-multiple').style.display = tipo === 'opcion_multiple' ? '' : 'none';
  document.getElementById('section-vf').style.display       = tipo === 'verdadero_falso' ? '' : 'none';
  document.getElementById('section-codigo').style.display   = tipo === 'codigo'          ? '' : 'none';
}

function onVFCheck(idx) {
  const other = idx === 0 ? 1 : 0;
  document.getElementById('vf_c_' + other).checked = false;
  document.getElementById('vf_correcta_val').value  = idx;
}

document.addEventListener('DOMContentLoaded', () => {
  [0, 1].forEach(i => {
    const cb = document.getElementById('vf_c_' + i);
    if (cb) cb.addEventListener('change', () => { if (cb.checked) onVFCheck(i); });
  });
});

function addOption() {
  if (optCount >= MAX_OPTS) {
    document.getElementById('add-opt-btn').disabled = true;
    return;
  }
  const i   = optCount;
  const row = document.createElement('div');
  row.className = 'opt-item opt-item--removable';
  row.id = 'opt-row-' + i;
  row.innerHTML = `
    <div class="opt-check">
      <input type="checkbox" name="opt_correcta[${i}]" id="opt_c_${i}">
      <span class="opt-check-lbl">OK</span>
    </div>
    <input type="text" name="opt_texto[${i}]" placeholder="Texto de la opcion ${i + 1}">
    <input type="text" name="opt_retro[${i}]" placeholder="Retroalimentacion (opcional)">
    <button type="button" class="opt-remove" onclick="removeOption(${i})" title="Eliminar">x</button>
  `;
  document.getElementById('options-container').appendChild(row);
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
