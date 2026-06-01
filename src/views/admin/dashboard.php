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
  <title>Panel de Administración — Loopbook</title>
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/modules/admin.css') ?>">
</head>
<body>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="main-content">
<div class="page-container">

  <div class="admin-page-header">
    <div>
      <h1 class="page-title gradient-text">⚙️ Panel de Administración</h1>
      <p class="page-subtitle">Gestiona módulos, lecciones y ejercicios del curso</p>
    </div>
    <a href="<?= base_url('index.php?page=dashboard') ?>" class="btn btn-outline btn-sm">← Volver al inicio</a>
  </div>

  <?php if ($ok): ?>
    <div class="alert alert-success mb-4"><?= e($ok) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-error mb-4"><?= e($error) ?></div>
  <?php endif; ?>

  <!-- Estadísticas -->
  <div class="admin-stats-grid">
    <div class="admin-stat-card admin-stat-card--blue">
      <div class="admin-stat-card__icon">📦</div>
      <div class="admin-stat-card__value"><?= $stats['modulos'] ?></div>
      <div class="admin-stat-card__label">Módulos</div>
    </div>
    <div class="admin-stat-card admin-stat-card--purple">
      <div class="admin-stat-card__icon">📄</div>
      <div class="admin-stat-card__value"><?= $stats['lecciones'] ?></div>
      <div class="admin-stat-card__label">Lecciones</div>
    </div>
    <div class="admin-stat-card admin-stat-card--orange">
      <div class="admin-stat-card__icon">✏️</div>
      <div class="admin-stat-card__value"><?= $stats['ejercicios'] ?></div>
      <div class="admin-stat-card__label">Ejercicios</div>
    </div>
    <div class="admin-stat-card admin-stat-card--green">
      <div class="admin-stat-card__icon">👥</div>
      <div class="admin-stat-card__value"><?= $stats['usuarios'] ?></div>
      <div class="admin-stat-card__label">Usuarios</div>
    </div>
    <div class="admin-stat-card admin-stat-card--cyan">
      <div class="admin-stat-card__icon">🏆</div>
      <div class="admin-stat-card__value"><?= $stats['progresos'] ?></div>
      <div class="admin-stat-card__label">Ejercicios completados</div>
    </div>
  </div>

  <!-- Accesos rápidos -->
  <div class="admin-quick-grid">
    <a href="<?= base_url('index.php?page=admin&action=modules') ?>" class="admin-quick-card">
      <span class="admin-quick-card__icon">📦</span>
      <div>
        <strong>Gestionar Módulos</strong>
        <p>Crear, editar y eliminar módulos del curso</p>
      </div>
      <span class="admin-quick-card__arrow">→</span>
    </a>
    <a href="<?= base_url('index.php?page=admin&action=module_create') ?>" class="admin-quick-card admin-quick-card--accent">
      <span class="admin-quick-card__icon">➕</span>
      <div>
        <strong>Nuevo Módulo</strong>
        <p>Agregar un módulo al curso</p>
      </div>
      <span class="admin-quick-card__arrow">→</span>
    </a>
  </div>

</div>
</main>
<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
