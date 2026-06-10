<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar contraseña — Loopbook</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><defs><linearGradient id=%22g%22 x1=%220%22 y1=%220%22 x2=%2240%22 y2=%2240%22 gradientUnits=%22userSpaceOnUse%22><stop offset=%220%25%22 stop-color=%22%23cc0000%22/><stop offset=%2255%25%22 stop-color=%22%23ff2800%22/><stop offset=%22100%25%22 stop-color=%22%23ff6b00%22/></linearGradient></defs><rect width=%2240%22 height=%2240%22 rx=%2211%22 fill=%22url(%23g)%22/><text x=%2250%25%22 y=%2257%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2213%22 font-weight=%22700%22 fill=%22white%22>%3C/%3E</text></svg>">
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body class="auth-body<?= (!empty($error) || !empty($success)) ? ' no-anim' : '' ?>">

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
        <img src="<?= base_url('img/loopbook_logo.png') ?>" class="auth-logo__icon" alt="Loopbook logo" style="border-radius:14px;">
        <div class="auth-logo__ring"></div>
      </div>
      <span class="auth-logo__name">Loopbook</span>
    </div>

    <h2 class="auth-title">¿Olvidaste tu contraseña?</h2>
    <p class="auth-subtitle">Ingresa tu correo y te enviaremos un enlace para restablecerla</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif; ?>

    <?php if (empty($success)): ?>
    <form method="POST" action="<?= base_url('index.php?page=forgot') ?>" class="auth-form" novalidate>
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
      <div class="form-group">
        <label for="email">Correo electrónico</label>
        <div class="input-icon-wrap">
          <span class="input-icon">✉️</span>
          <input type="email" id="email" name="email"
                 placeholder="tu@correo.com" required autocomplete="email">
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-full auth-submit-btn">
        Enviar enlace de recuperación
      </button>
    </form>
    <?php endif; ?>

    <p class="auth-switch">
      <a href="<?= base_url('index.php?page=login') ?>">← Volver al inicio de sesión</a>
    </p>
  </div>
</div>

<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
