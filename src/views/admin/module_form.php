<?php
$isEdit = isset($moduleRow) && $moduleRow !== null;
$title  = $isEdit ? 'Editar Módulo' : 'Nuevo Módulo';
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
    .mf-label {
      display: block;
      font-size: .78rem;
      font-weight: 700;
      color: #374151;
      text-transform: uppercase;
      letter-spacing: .04em;
      margin-bottom: .4rem;
    }
    .mf-req { color: #7c3aed; }
    .mf-input, .mf-textarea, .mf-select {
      width: 100%;
      padding: .65rem .9rem;
      border: 1.5px solid #e5e7eb;
      border-radius: 9px;
      font-size: .93rem;
      font-family: inherit;
      outline: none;
      background: #fafafa;
      color: #111827;
      transition: border-color .15s, box-shadow .15s;
    }
    .mf-input:focus, .mf-textarea:focus, .mf-select:focus {
      border-color: #7c3aed;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(124,58,237,.1);
    }
    .mf-input:disabled, .mf-select:disabled {
      background: #f3f4f6; color: #9ca3af; cursor: not-allowed;
    }
    .mf-textarea { resize: vertical; min-height: 100px; }
    .mf-order-row { display: flex; align-items: center; gap: .75rem; }
    .mf-input--order { width: 90px; text-align: center; flex-shrink: 0; }
    .mf-hint { font-size: .78rem; color: #6b7280; }
    .mf-actions {
      display: flex; gap: .75rem; justify-content: flex-end;
      padding-top: 1.25rem;
      border-top: 1px solid #f3f4f6;
      margin-top: .5rem;
    }
    html.dark .mf-input, html.dark .mf-textarea, html.dark .mf-select {
      background: var(--bg); border-color: var(--border); color: var(--text);
    }
  </style>
</head>
<body>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="main-content">
<div class="page-container page-container--narrow">

  <!-- Breadcrumb -->
  <div class="admin-page-header">
    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
      <a href="<?= base_url('index.php?page=admin') ?>"
         class="btn btn-outline btn-sm" style="font-size:.78rem;padding:.28rem .7rem">
        Admin
      </a>
      <span style="color:var(--gray-400)">›</span>
      <a href="<?= base_url('index.php?page=admin&action=modules') ?>"
         class="btn btn-outline btn-sm" style="font-size:.78rem;padding:.28rem .7rem">
        Modulos
      </a>
      <span style="color:var(--gray-400)">›</span>
      <span style="font-size:.88rem;font-weight:600;color:#111827"><?= $title ?></span>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error mb-4"><?= e($error) ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-stripe" style="height:5px;background:linear-gradient(90deg,#4f46e5,#7c3aed,#a855f7)"></div>
    <div class="card-body">
      <h2 style="font-size:1.4rem;font-weight:900;color:#111827;margin:0 0 1.75rem"><?= $title ?></h2>

      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <!-- Curso -->
        <div class="form-group">
          <label class="mf-label" for="curso_id">Curso</label>
          <select name="curso_id" id="curso_id" class="mf-select"
                  <?= $isEdit ? 'disabled' : '' ?>>
            <?php foreach ($courses as $c): ?>
            <option value="<?= (int)$c['id_curso'] ?>"
              <?= $isEdit && (int)$moduleRow['id_curso'] === (int)$c['id_curso'] ? 'selected' : '' ?>>
              <?= e($c['nombre']) ?>
            </option>
            <?php endforeach; ?>
          </select>
          <?php if ($isEdit): ?>
            <input type="hidden" name="curso_id" value="<?= (int)$moduleRow['id_curso'] ?>">
          <?php endif; ?>
        </div>

        <!-- Nombre -->
        <div class="form-group">
          <label class="mf-label" for="nombre">
            Nombre del módulo <span class="mf-req">*</span>
          </label>
          <input type="text" id="nombre" name="nombre" class="mf-input"
                 value="<?= e($isEdit ? $moduleRow['nombre'] : '') ?>"
                 placeholder="Ej: Variables y Tipos de Datos"
                 required maxlength="150">
        </div>

        <!-- Descripción -->
        <div class="form-group">
          <label class="mf-label" for="descripcion">Descripción</label>
          <textarea id="descripcion" name="descripcion" class="mf-textarea"
                    placeholder="Breve descripción del módulo que verán los estudiantes..."><?= e($isEdit ? ($moduleRow['descripcion'] ?? '') : '') ?></textarea>
        </div>

        <!-- Orden -->
        <div class="form-group">
          <label class="mf-label" for="orden">Orden de aparición</label>
          <div class="mf-order-row">
            <input type="number" id="orden" name="orden" class="mf-input mf-input--order"
                   value="<?= $isEdit ? (int)$moduleRow['orden'] : (int)$nextOrder ?>"
                   min="1" max="999">
            <span class="mf-hint">
              Determina la posición del módulo. Los módulos se muestran de menor a mayor.
            </span>
          </div>
        </div>

        <!-- Acciones -->
        <div class="mf-actions">
          <a href="<?= base_url('index.php?page=admin&action=modules') ?>"
             class="btn btn-outline">Cancelar</a>
          <button type="submit" class="btn btn-primary">
            <?= $isEdit ? 'Guardar cambios' : 'Crear módulo' ?>
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
</main>
<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
