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
  <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
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
      <span class="admin-breadcrumb"><?= $title ?></span>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error mb-4"><?= e($error) ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-stripe card-stripe--purple"></div>
    <div class="card-body">
      <h2 class="admin-form-title"><?= $isEdit ? '✏️' : '➕' ?> <?= $title ?></h2>

      <form method="POST" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <div class="form-group">
          <label for="curso_id">Curso</label>
          <select name="curso_id" id="curso_id" class="admin-select" <?= $isEdit ? 'disabled' : '' ?>>
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

        <div class="form-group">
          <label for="nombre">Nombre del módulo <span class="required">*</span></label>
          <input type="text" id="nombre" name="nombre" class="admin-input"
                 value="<?= e($isEdit ? $moduleRow['nombre'] : '') ?>"
                 placeholder="Ej: Variables y Tipos de Datos" required maxlength="150">
        </div>

        <div class="form-group">
          <label for="descripcion">Descripción</label>
          <textarea id="descripcion" name="descripcion" class="admin-textarea" rows="3"
                    placeholder="Breve descripción del módulo…"><?= e($isEdit ? ($moduleRow['descripcion'] ?? '') : '') ?></textarea>
        </div>

        <div class="form-group">
          <label for="orden">Orden de aparición</label>
          <input type="number" id="orden" name="orden" class="admin-input admin-input--sm"
                 value="<?= $isEdit ? (int)$moduleRow['orden'] : (int)$nextOrder ?>" min="1" max="999">
          <small class="form-hint">Número que determina la posición del módulo en el curso.</small>
        </div>

        <div class="admin-form-actions">
          <a href="<?= base_url('index.php?page=admin&action=modules') ?>" class="btn btn-outline">Cancelar</a>
          <button type="submit" class="btn btn-primary">
            <?= $isEdit ? '💾 Guardar cambios' : '➕ Crear módulo' ?>
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
