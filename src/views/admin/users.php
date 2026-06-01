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
  <title>Usuarios — Admin Loopbook</title>
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
      <h1 class="page-title gradient-text" style="display:inline">Usuarios</h1>
    </div>
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
          <th>Email</th>
          <th>Rol</th>
          <th>Ejercicios completados</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($users)): ?>
          <tr><td colspan="6" class="admin-table__empty">No hay usuarios registrados.</td></tr>
        <?php else: ?>
          <?php foreach ($users as $u): ?>
          <tr>
            <td><?= (int)$u['id_usuario'] ?></td>
            <td><?= e($u['nombre']) ?></td>
            <td><?= e($u['correo']) ?></td>
            <td>
              <?php if (!empty($u['is_superadmin'])): ?>
                <span class="badge badge--admin">Superadmin</span>
              <?php elseif ($u['is_admin']): ?>
                <span class="badge badge--admin">Admin</span>
              <?php else: ?>
                <span class="badge">Usuario</span>
              <?php endif; ?>
            </td>
            <td><?= (int)$u['ejercicios_completados'] ?></td>
            <td>
              <a href="<?= base_url('index.php?page=admin&action=user_progress&user_id=' . (int)$u['id_usuario']) ?>"
                 class="btn btn-outline btn-xs">Ver progreso</a>
              <?php if (!empty($_SESSION['is_superadmin']) && empty($u['is_superadmin']) && (int)$u['id_usuario'] !== (int)$_SESSION['user_id']): ?>
              <form method="POST" action="<?= base_url('index.php?page=admin&action=toggle_admin') ?>" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="user_id" value="<?= (int)$u['id_usuario'] ?>">
                <button type="submit" class="btn btn-xs <?= $u['is_admin'] ? 'btn-danger' : 'btn-outline' ?>">
                  <?= $u['is_admin'] ? 'Quitar Admin' : 'Hacer Admin' ?>
                </button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
</main>
<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
