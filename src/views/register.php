<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear Cuenta — Loopbook</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><defs><linearGradient id=%22g%22 x1=%220%22 y1=%220%22 x2=%2240%22 y2=%2240%22 gradientUnits=%22userSpaceOnUse%22><stop offset=%220%25%22 stop-color=%22%23cc0000%22/><stop offset=%2255%25%22 stop-color=%22%23ff2800%22/><stop offset=%22100%25%22 stop-color=%22%23ff6b00%22/></linearGradient></defs><rect width=%2240%22 height=%2240%22 rx=%2211%22 fill=%22url(%23g)%22/><text x=%2250%25%22 y=%2257%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2213%22 font-weight=%22700%22 fill=%22white%22>%3C/%3E</text></svg>">
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body class="auth-body auth-body--register<?= (!empty($error) || !empty($lastUsername) || !empty($lastEmail)) ? ' no-anim' : '' ?>">

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
  <div class="auth-blob auth-blob--3"></div>
  <div class="auth-blob auth-blob--4"></div>
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
      <span class="auth-logo__name" id="authLogoName"></span>
    </div>

    <h2 class="auth-title">¡Únete a Loopbook!</h2>
    <p class="auth-subtitle">Comienza tu aventura de programación</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('index.php?page=register') ?>" class="auth-form" novalidate>
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
      <div class="form-group">
        <label for="username">Usuario</label>
        <div class="input-icon-wrap">
          <span class="input-icon">👤</span>
          <input type="text" id="username" name="username"
                 placeholder="Elige un nombre de usuario" required autocomplete="username"
                 value="<?= e($lastUsername ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <div class="input-icon-wrap">
          <span class="input-icon">✉️</span>
          <input type="email" id="email" name="email"
                 placeholder="tu@email.com" required autocomplete="email"
                 value="<?= e($lastEmail ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <div class="input-icon-wrap">
          <span class="input-icon">🔒</span>
          <input type="password" id="password" name="password"
                 placeholder="Mínimo 6 caracteres" required autocomplete="new-password">
        </div>
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirmar Contraseña</label>
        <div class="input-icon-wrap">
          <span class="input-icon">🔒</span>
          <input type="password" id="confirm_password" name="confirm_password"
                 placeholder="Repite tu contraseña" required autocomplete="new-password">
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-full auth-submit-btn">Crear mi cuenta</button>
    </form>

    <p class="auth-switch">
      <a href="<?= base_url('index.php?page=login') ?>">← Volver al inicio de sesión</a>
    </p>
  </div>
</div>

<script src="<?= base_url('js/app.js') ?>"></script>
<script>
(function() {
  const el        = document.getElementById('authLogoName');
  const text      = 'Loopbook';
  const hasError  = <?= !empty($error) ? 'true' : 'false' ?>;
  const savedUser = <?= json_encode($lastUsername ?? '') ?>;
  const savedEmail = <?= json_encode($lastEmail ?? '') ?>;

  if (hasError || savedUser || savedEmail) {
    el.textContent = text;
  } else {
    let i = 0;
    function type() {
      if (i <= text.length) {
        el.textContent = text.slice(0, i);
        i++;
        setTimeout(type, i === 1 ? 400 : 80);
      }
    }
    setTimeout(type, 600);
  }

  const fields = ['username', 'email', 'password', 'confirm_password'];
  fields.forEach(function(id, idx) {
    const field = document.getElementById(id);
    if (!field) return;
    if (idx < fields.length - 1) {
      field.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          document.getElementById(fields[idx + 1]).focus();
        }
      });
    }
  });

  // Bloquear caracteres especiales en usuario (solo letras, números y _)
  const usernameField = document.getElementById('username');
  usernameField.addEventListener('keypress', function(e) {
    if (!/[a-zA-Z0-9_]/.test(e.key)) e.preventDefault();
  });
  usernameField.addEventListener('paste', function(e) {
    e.preventDefault();
    const clean = (e.clipboardData || window.clipboardData).getData('text').replace(/[^a-zA-Z0-9_]/g, '');
    document.execCommand('insertText', false, clean);
  });

  // Bloquear espacios en contraseña y confirmación
  ['password', 'confirm_password'].forEach(function(id) {
    const f = document.getElementById(id);
    if (!f) return;
    f.addEventListener('keypress', function(e) {
      if (e.key === ' ') e.preventDefault();
    });
  });
})();
</script>
</body>
</html>
