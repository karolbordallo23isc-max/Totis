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
  <title>Lecciones — Admin Loopbook</title>
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
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
      <span class="admin-breadcrumb"><?= e($moduleName) ?></span>
    </div>
    <a href="<?= base_url('index.php?page=admin&action=lesson_create&module_id=' . $moduleId) ?>"
       class="btn btn-primary btn-sm">➕ Nueva lección</a>
  </div>

  <?php if ($ok): ?>
    <div class="alert alert-success mb-4"><?= e($ok) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-error mb-4"><?= e($error) ?></div>
  <?php endif; ?>

  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Título</th>
          <th>Tipo</th>
          <th>Orden</th>
          <th>Ejercicios</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lessons as $l): ?>
        <tr>
          <td><?= (int)$l['id_contenido'] ?></td>
          <td>
            <strong><?= e($l['titulo']) ?></strong>
            <?php if ($l['tipo'] === 'video' && $l['url']): ?>
              <br><small class="admin-url-preview">🎬 <?= e(mb_substr($l['url'], 0, 50)) ?>…</small>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($l['tipo'] === 'video'): ?>
              <span class="admin-badge admin-badge--purple">🎬 Video</span>
            <?php elseif ($l['tipo'] === 'imagen'): ?>
              <span class="admin-badge admin-badge--cyan">🖼️ Imagen</span>
            <?php else: ?>
              <span class="admin-badge admin-badge--blue">📄 Texto</span>
            <?php endif; ?>
          </td>
          <td><?= (int)$l['orden'] ?></td>
          <td>
            <a href="<?= base_url('index.php?page=admin&action=exercises&lesson_id=' . $l['id_contenido']) ?>"
               class="admin-badge admin-badge--orange">
              ✏️ <?= (int)$l['total_ejercicios'] ?>
            </a>
          </td>
          <td class="admin-table__actions">
            <a href="<?= base_url('index.php?page=admin&action=exercises&lesson_id=' . $l['id_contenido']) ?>"
               class="btn btn-outline btn-xs">✏️ Ejercicios</a>
            <a href="<?= base_url('index.php?page=admin&action=lesson_edit&id=' . $l['id_contenido']) ?>"
               class="btn btn-outline btn-xs">📝 Editar</a>
            <form method="POST" action="<?= base_url('index.php?page=admin&action=lesson_delete') ?>"
                  onsubmit="return confirm('¿Eliminar esta lección y sus ejercicios?')">
              <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
              <input type="hidden" name="id"        value="<?= (int)$l['id_contenido'] ?>">
              <input type="hidden" name="module_id" value="<?= (int)$moduleId ?>">
              <button type="submit" class="btn btn-danger btn-xs">🗑️</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($lessons)): ?>
        <tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:2rem">No hay lecciones en este módulo.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
</main>
<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
