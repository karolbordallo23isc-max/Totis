<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Perfil — Loopbook</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><defs><linearGradient id=%22g%22 x1=%220%22 y1=%220%22 x2=%2240%22 y2=%2240%22 gradientUnits=%22userSpaceOnUse%22><stop offset=%220%25%22 stop-color=%22%23cc0000%22/><stop offset=%2255%25%22 stop-color=%22%23ff2800%22/><stop offset=%22100%25%22 stop-color=%22%23ff6b00%22/></linearGradient></defs><rect width=%2240%22 height=%2240%22 rx=%2211%22 fill=%22url(%23g)%22/><text x=%2250%25%22 y=%2257%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2213%22 font-weight=%22700%22 fill=%22white%22>%3C/%3E</text></svg>">
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<main class="main-content">
<div class="page-container page-container--narrow">

  <h1 class="page-title gradient-text">Mi Perfil</h1>
  <p class="page-subtitle">Personaliza tu cuenta y elige tu avatar</p>

  <?php if (!empty($error)): ?>
    <div class="alert alert-error mb-4"><?= e($error) ?></div>
  <?php endif; ?>
  <?php if (!empty($success)): ?>
    <div class="alert alert-success mb-4"><?= e($success) ?></div>
  <?php endif; ?>

  <div class="card mb-4">
    <div class="card-stripe card-stripe--purple"></div>
    <div class="card-body">
      <div class="card-section-title">
        <span class="card-section-icon card-section-icon--purple">👤</span>
        <h3>Información de perfil</h3>
      </div>

      <form method="POST" action="<?= base_url('index.php?page=profile') ?>">
        <input type="hidden" name="action" value="profile">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <div class="profile-avatar-wrap">
          <div class="profile-avatar-current" id="avatarPreview">
            <?= e($user['avatar'] ?? '👤') ?>
          </div>
          <div>
            <p class="text-sm font-bold" style="margin-bottom:.5rem;">Elige tu avatar</p>
            <div class="avatar-grid">
              <?php foreach ($avatars as $av): ?>
              <button type="button"
                      class="avatar-btn <?= ($user['avatar'] ?? '👤') === $av ? 'avatar-btn--active' : '' ?>"
                      data-avatar="<?= e($av) ?>"
                      onclick="selectAvatar(this)">
                <?= e($av) ?>
              </button>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <input type="hidden" name="avatar" id="avatarInput" value="<?= e($user['avatar'] ?? '👤') ?>">

        <div class="form-group mt-4">
          <label for="username">Nombre de usuario</label>
          <div class="input-icon-wrap">
            <span class="input-icon">👤</span>
            <input type="text" id="username" name="username"
                   value="<?= e($user['usuario'] ?? '') ?>"
                   placeholder="Tu nombre de usuario" required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-full mt-4">Guardar cambios</button>
      </form>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-stripe card-stripe--orange"></div>
    <div class="card-body">
      <div class="card-section-title">
        <span class="card-section-icon card-section-icon--orange">🔒</span>
        <h3>Cambiar contraseña</h3>
      </div>

      <form method="POST" action="<?= base_url('index.php?page=profile') ?>">
        <input type="hidden" name="action" value="password">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <div class="auth-form">
          <div class="form-group">
            <label for="current_password">Contraseña actual</label>
            <div class="input-icon-wrap">
              <span class="input-icon">🔒</span>
              <input type="password" id="current_password" name="current_password"
                     placeholder="Tu contraseña actual" required>
            </div>
          </div>
          <div class="form-group">
            <label for="new_password">Nueva contraseña</label>
            <div class="input-icon-wrap">
              <span class="input-icon">🔑</span>
              <input type="password" id="new_password" name="new_password"
                     placeholder="Mínimo 6 caracteres" required>
            </div>
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirmar nueva contraseña</label>
            <div class="input-icon-wrap">
              <span class="input-icon">🔑</span>
              <input type="password" id="confirm_password" name="confirm_password"
                     placeholder="Repite la nueva contraseña" required>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-full">Cambiar contraseña</button>
        </div>
      </form>
    </div>
  </div>

</div>
</main>

<script>
  function selectAvatar(btn) {
    document.querySelectorAll('.avatar-btn').forEach(b => b.classList.remove('avatar-btn--active'));
    btn.classList.add('avatar-btn--active');
    const av = btn.dataset.avatar;
    document.getElementById('avatarInput').value        = av;
    document.getElementById('avatarPreview').textContent = av;
  }
</script>
<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
