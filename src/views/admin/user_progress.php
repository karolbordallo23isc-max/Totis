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
  <title>Progreso de <?= e($user['nombre']) ?> — Admin Loopbook</title>
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
      <a href="<?= base_url('index.php?page=admin&action=users') ?>" class="admin-breadcrumb">Usuarios</a>
      <span class="admin-breadcrumb-sep">›</span>
      <h1 class="page-title gradient-text" style="display:inline">Progreso: <?= e($user['nombre']) ?></h1>
    </div>
    <a href="<?= base_url('index.php?page=admin&action=users') ?>" class="btn btn-outline btn-sm">← Volver</a>
  </div>

  <p class="page-subtitle"><?= e($user['correo']) ?></p>

  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Módulo</th>
          <th>Lección</th>
          <th>Ejercicio</th>
          <th>Estado</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($progress)): ?>
          <tr><td colspan="5" class="admin-table__empty">Este usuario no tiene progreso registrado.</td></tr>
        <?php else: ?>
          <?php foreach ($progress as $row): ?>
          <tr>
            <td><?= e($row['modulo']) ?></td>
            <td><?= e($row['leccion']) ?></td>
            <td><?= e($row['pregunta']) ?></td>
            <td><?= $row['completado'] ? '<span class="badge badge--success">✔ Completado</span>' : '<span class="badge">Pendiente</span>' ?></td>
            <td><?= $row['fecha_completado'] ? e($row['fecha_completado']) : '—' ?></td>
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
