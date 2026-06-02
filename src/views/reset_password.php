<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nueva contraseña — Loopbook</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><defs><linearGradient id=%22g%22 x1=%220%22 y1=%220%22 x2=%2240%22 y2=%2240%22 gradientUnits=%22userSpaceOnUse%22><stop offset=%220%25%22 stop-color=%22%23cc0000%22/><stop offset=%2255%25%22 stop-color=%22%23ff2800%22/><stop offset=%22100%25%22 stop-color=%22%23ff6b00%22/></linearGradient></defs><rect width=%2240%22 height=%2240%22 rx=%2211%22 fill=%22url(%23g)%22/><text x=%2250%25%22 y=%2257%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2213%22 font-weight=%22700%22 fill=%22white%22>%3C/%3E</text></svg>">
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body class="auth-body<?= !empty($error) ? ' no-anim' : '' ?>">

<div class="auth-particles" aria-hidden="true">
  <span class="auth-particle">{ }</span>
  <span class="auth-particle">&lt;/&gt;</span>
  <span class="auth-particle">( )</span>
  <span class="auth-particle">01</span>
  <span class="auth-particle">[ ]</span>
  <span class="auth-particle">=&gt;</span>
  <span class="auth-particle">&&</span>
  <span class="auth-particle">++</span>
  <span class="auth-particle">fn()</span>
  <span class="auth-particle">::</span>
  <span class="auth-particle">/**</span>
  <span class="auth-particle">if</span>
</div>

<div class="auth-bg">
  <div class="auth-blob auth-blob--1"></div>
  <div class="auth-blob auth-blob--2"></div>
</div>

<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-corner"></div>
    <div class="auth-corner-tl"></div>

    <div class="auth-logo">
      <div class="auth-logo__icon-wrap">
        <img src="/loopbook/public/img/loopbook_logo.png" class="auth-logo__icon" alt="Loopbook logo" style="border-radius:14px;">
        <div class="auth-logo__ring"></div>
      </div>
      <span class="auth-logo__name">Loopbook</span>
    </div>

    <h2 class="auth-title">Crear nueva contraseña</h2>
    <p class="auth-subtitle">Elige una contraseña segura de al menos 6 caracteres</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('index.php?page=reset') ?>" class="auth-form" novalidate>
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
      <input type="hidden" name="token" value="<?= e($_GET['token'] ?? '') ?>">

      <div class="form-group">
        <label for="password">Nueva contraseña</label>
        <div class="input-icon-wrap">
          <span class="input-icon">🔒</span>
          <input type="password" id="password" name="password"
                 placeholder="Mínimo 6 caracteres" required autocomplete="new-password">
        </div>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirmar contraseña</label>
        <div class="input-icon-wrap">
          <span class="input-icon">🔒</span>
          <input type="password" id="confirm_password" name="confirm_password"
                 placeholder="Repite la contraseña" required autocomplete="new-password">
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-full auth-submit-btn">
        Guardar nueva contraseña
      </button>
    </form>

    <p class="auth-switch">
      <a href="<?= base_url('index.php?page=login') ?>">← Volver al inicio de sesión</a>
    </p>
  </div>
</div>

<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
