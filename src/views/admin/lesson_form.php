<?php
$isEdit = isset($lessonRow) && $lessonRow !== null;
$title  = $isEdit ? 'Editar Lección' : 'Nueva Lección';
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
      <span class="admin-breadcrumb"><?= $title ?></span>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error mb-4"><?= e($error) ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-stripe card-stripe--purple"></div>
    <div class="card-body">
      <h2 class="admin-form-title"><?= $isEdit ? '📝' : '➕' ?> <?= $title ?></h2>

      <form method="POST" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <input type="hidden" name="module_id"  value="<?= (int)$moduleId ?>">

        <div class="form-group">
          <label for="titulo">Título de la lección <span class="required">*</span></label>
          <input type="text" id="titulo" name="titulo" class="admin-input"
                 value="<?= e($isEdit ? $lessonRow['titulo'] : '') ?>"
                 placeholder="Ej: ¿Qué es una variable?" required maxlength="150">
        </div>

        <div class="form-group">
          <label for="tipo">Tipo de contenido</label>
          <select id="tipo" name="tipo" class="admin-select" onchange="toggleUrlField(this.value)">
            <option value="texto"  <?= (!$isEdit || $lessonRow['tipo'] === 'texto')  ? 'selected' : '' ?>>📄 Texto</option>
            <option value="video"  <?= ($isEdit && $lessonRow['tipo'] === 'video')   ? 'selected' : '' ?>>🎬 Video (URL)</option>
            <option value="imagen" <?= ($isEdit && $lessonRow['tipo'] === 'imagen')  ? 'selected' : '' ?>>🖼️ Imagen (URL)</option>
          </select>
        </div>

        <div class="form-group" id="url-field" style="<?= ($isEdit && $lessonRow['tipo'] === 'texto') ? 'display:none' : '' ?>">
          <label for="url">URL del recurso</label>
          <input type="url" id="url" name="url" class="admin-input"
                 value="<?= e($isEdit ? ($lessonRow['url'] ?? '') : '') ?>"
                 placeholder="https://www.youtube.com/embed/...">
          <small class="form-hint">Para videos de YouTube usa el formato embed: https://www.youtube.com/embed/VIDEO_ID</small>
        </div>

        <div class="form-group">
          <label for="texto">Contenido teórico</label>
          <textarea id="texto" name="texto" class="admin-textarea admin-textarea--lg" rows="8"
                    placeholder="Escribe el contenido explicativo de la lección…"><?= e($isEdit ? ($lessonRow['texto'] ?? '') : '') ?></textarea>
          <small class="form-hint">Explica el tema con detalle. Este texto aparece antes de los ejercicios.</small>
        </div>

        <div class="form-group">
          <label for="orden">Orden en el módulo</label>
          <input type="number" id="orden" name="orden" class="admin-input admin-input--sm"
                 value="<?= $isEdit ? (int)$lessonRow['orden'] : (int)$nextOrder ?>" min="1" max="999">
        </div>

        <div class="admin-form-actions">
          <a href="<?= base_url('index.php?page=admin&action=lessons&module_id=' . $moduleId) ?>" class="btn btn-outline">Cancelar</a>
          <button type="submit" class="btn btn-primary">
            <?= $isEdit ? '💾 Guardar cambios' : '➕ Crear lección' ?>
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
</main>
<script src="<?= base_url('js/app.js') ?>"></script>
<script>
function toggleUrlField(tipo) {
  document.getElementById('url-field').style.display = tipo === 'texto' ? 'none' : '';
}
// Bloquear < y > en el título de la lección
document.getElementById('titulo').addEventListener('keypress', function(e) {
  if (e.key === '<' || e.key === '>') e.preventDefault();
});
</script>
</body>
</html>
