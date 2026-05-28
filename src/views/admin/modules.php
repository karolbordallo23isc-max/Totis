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
  <title>Módulos — Admin Loopbook</title>
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
      <h1 class="page-title gradient-text" style="display:inline">Módulos</h1>
    </div>
    <a href="<?= base_url('index.php?page=admin&action=module_create') ?>" class="btn btn-primary btn-sm">➕ Nuevo módulo</a>
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
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Orden</th>
          <th>Lecciones</th>
          <th>Ejercicios</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($modules as $m): ?>
        <tr>
          <td><?= (int)$m['id_modulo'] ?></td>
          <td><strong><?= e($m['nombre']) ?></strong></td>
          <td class="admin-table__desc"><?= e(mb_substr($m['descripcion'] ?? '', 0, 80)) ?>…</td>
          <td><?= (int)$m['orden'] ?></td>
          <td>
            <a href="<?= base_url('index.php?page=admin&action=lessons&module_id=' . $m['id_modulo']) ?>"
               class="admin-badge admin-badge--blue">
              📄 <?= (int)$m['total_lecciones'] ?>
            </a>
          </td>
          <td><span class="admin-badge admin-badge--orange">✏️ <?= (int)$m['total_ejercicios'] ?></span></td>
          <td class="admin-table__actions">
            <a href="<?= base_url('index.php?page=admin&action=lessons&module_id=' . $m['id_modulo']) ?>"
               class="btn btn-outline btn-xs" title="Ver lecciones">📄 Lecciones</a>
            <a href="<?= base_url('index.php?page=admin&action=module_edit&id=' . $m['id_modulo']) ?>"
               class="btn btn-outline btn-xs" title="Editar">✏️ Editar</a>
            <form method="POST" action="<?= base_url('index.php?page=admin&action=module_delete') ?>"
                  onsubmit="return confirm('¿Eliminar este módulo y todo su contenido? Esta acción no se puede deshacer.')">
              <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
              <input type="hidden" name="id" value="<?= (int)$m['id_modulo'] ?>">
              <button type="submit" class="btn btn-danger btn-xs">🗑️ Eliminar</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($modules)): ?>
        <tr><td colspan="7" style="text-align:center;color:#9ca3af;padding:2rem">No hay módulos aún.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
</main>
<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
