<?php
$ok    = $_SESSION['admin_ok']    ?? '';
$error = $_SESSION['admin_error'] ?? '';
unset($_SESSION['admin_ok'], $_SESSION['admin_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ejercicios — Admin Loopbook</title>
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/modules/admin.css') ?>">
</head>
<body>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="main-content">
<div class="page-container">

  <div class="admin-page-header">
    <div>
      <a href="<?= base_url('index.php?page=admin') ?>" class="admin-breadcrumb">⚙️ Admin</a>
      <span class="admin-breadcrumb-sep">›</span>
      <a href="<?= base_url('index.php?page=admin&action=modules') ?>" class="admin-breadcrumb">Módulos</a>
      <span class="admin-breadcrumb-sep">›</span>
      <a href="<?= base_url('index.php?page=admin&action=lessons&module_id=' . $moduleId) ?>" class="admin-breadcrumb">Lecciones</a>
      <span class="admin-breadcrumb-sep">›</span>
      <span class="admin-breadcrumb"><?= e($lesson['titulo']) ?></span>
    </div>
    <a href="<?= base_url('index.php?page=admin&action=exercise_create&lesson_id=' . $lessonId) ?>"
       class="btn btn-primary btn-sm">➕ Nuevo ejercicio</a>
  </div>

  <?php if ($ok): ?>
    <div class="alert alert-success mb-4"><?= e($ok) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-error mb-4"><?= e($error) ?></div>
  <?php endif; ?>

  <?php if (empty($exercises)): ?>
    <div class="admin-empty-state">
      <p>✏️ No hay ejercicios en esta lección.</p>
      <a href="<?= base_url('index.php?page=admin&action=exercise_create&lesson_id=' . $lessonId) ?>"
         class="btn btn-primary">➕ Crear primer ejercicio</a>
    </div>
  <?php else: ?>
  <div class="admin-exercises-list">
    <?php foreach ($exercises as $ex): ?>
    <div class="admin-exercise-card">
      <div class="admin-exercise-card__header">
        <div class="admin-exercise-card__meta">
          <span class="admin-badge admin-badge--<?= $ex['tipo'] === 'verdadero_falso' ? 'cyan' : 'purple' ?>">
            <?= $ex['tipo'] === 'verdadero_falso' ? '✅ V/F' : '🔘 Opción múltiple' ?>
          </span>
          <span class="admin-badge admin-badge--blue"><?= count($ex['options']) ?> opciones</span>
        </div>
        <div class="admin-exercise-card__actions">
          <a href="<?= base_url('index.php?page=admin&action=exercise_edit&id=' . $ex['id_ejercicio']) ?>"
             class="btn btn-outline btn-xs">✏️ Editar</a>
          <form method="POST" action="<?= base_url('index.php?page=admin&action=exercise_delete') ?>"
                onsubmit="return confirm('¿Eliminar este ejercicio y sus opciones?')" style="display:inline">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id"        value="<?= (int)$ex['id_ejercicio'] ?>">
            <input type="hidden" name="lesson_id" value="<?= (int)$lessonId ?>">
            <button type="submit" class="btn btn-danger btn-xs">🗑️</button>
          </form>
        </div>
      </div>
      <p class="admin-exercise-card__question"><?= e($ex['pregunta']) ?></p>
      <?php if (!empty($ex['options'])): ?>
      <ul class="admin-options-list">
        <?php foreach ($ex['options'] as $opt): ?>
        <li class="admin-options-list__item <?= $opt['es_correcta'] ? 'admin-options-list__item--correct' : '' ?>">
          <?= $opt['es_correcta'] ? '✅' : '⬜' ?> <?= e($opt['texto']) ?>
          <?php if ($opt['retroalimentacion']): ?>
            <span class="admin-options-list__retro">— <?= e(mb_substr($opt['retroalimentacion'], 0, 60)) ?>…</span>
          <?php endif; ?>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
      <?php if ($ex['retroalimentacion']): ?>
        <p class="admin-exercise-card__retro">💡 <?= e(mb_substr($ex['retroalimentacion'], 0, 120)) ?>…</p>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</div>
</main>
<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
