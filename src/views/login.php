<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión — Loopbook</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><defs><linearGradient id=%22g%22 x1=%220%22 y1=%220%22 x2=%2240%22 y2=%2240%22 gradientUnits=%22userSpaceOnUse%22><stop offset=%220%25%22 stop-color=%22%23cc0000%22/><stop offset=%2255%25%22 stop-color=%22%23ff2800%22/><stop offset=%22100%25%22 stop-color=%22%23ff6b00%22/></linearGradient></defs><rect width=%2240%22 height=%2240%22 rx=%2211%22 fill=%22url(%23g)%22/><text x=%2250%25%22 y=%2257%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2213%22 font-weight=%22700%22 fill=%22white%22>%3C/%3E</text></svg>">
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
</head>
<body class="auth-body<?= (!empty($error) || $blocked || !empty($lastUsername)) ? ' no-anim' : '' ?>">

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
      <span class="auth-logo__name" id="authLogoName"></span>
    </div>

    <h2 class="auth-title">¡Bienvenido de vuelta!</h2>
    <p class="auth-subtitle">Continúa tu viaje de aprendizaje</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php
      $resetSuccess = $_SESSION['reset_success_login'] ?? '';
      unset($_SESSION['reset_success_login']);
    ?>
    <?php if (!empty($resetSuccess)): ?>
      <div class="alert alert-success"><?= e($resetSuccess) ?></div>
    <?php endif; ?>

    <?php if ($blocked): ?>
      <div class="alert alert-error" id="blockAlert">
        🔒 Acceso bloqueado. Espera <strong id="blockCountdown"><?= e($blockTime) ?></strong> antes de intentar de nuevo.
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('index.php?page=login') ?>" class="auth-form" novalidate>
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
      <div class="form-group">
        <label for="username">Usuario</label>
        <div class="input-icon-wrap">
          <span class="input-icon">👤</span>
          <input type="text" id="username" name="username"
                 placeholder="Ingresa tu usuario" required autocomplete="username">
        </div>
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <div class="input-icon-wrap">
          <span class="input-icon">🔒</span>
          <input type="password" id="password" name="password"
                 placeholder="Ingresa tu contraseña" required autocomplete="current-password">
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-full auth-submit-btn"
              <?= $blocked ? 'disabled' : '' ?>>
        <?= $blocked ? '🔒 Bloqueado' : 'Iniciar Sesión' ?>
      </button>
    </form>

    <p style="text-align:right; margin-top:.75rem; margin-bottom:.5rem; font-size:.85rem;">
      <a href="<?= base_url('index.php?page=forgot') ?>" style="color:var(--color-primary);">¿Olvidaste tu contraseña?</a>
    </p>

    <p class="auth-switch">
      ¿No tienes cuenta? <a href="<?= base_url('index.php?page=register') ?>">Regístrate aquí</a>
    </p>
  </div>
</div>

<script src="<?= base_url('js/app.js') ?>"></script>
<script>
(function() {
  const el        = document.getElementById('authLogoName');
  const text      = 'Loopbook';
  const hasError  = <?= (!empty($error) || $blocked) ? 'true' : 'false' ?>;
  const savedUser = <?= json_encode($lastUsername ?? '') ?>;

  if (hasError || savedUser) {
    el.textContent = text;
    const usernameField = document.getElementById('username');
    if (savedUser && usernameField.value === '') {
      usernameField.value = savedUser;
    }
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

  // Contador regresivo en tiempo real para el bloqueo
  <?php if ($blocked && $blockSeconds > 0): ?>
  (function() {
    let seconds = <?= (int)$blockSeconds ?>;
    const el    = document.getElementById('blockCountdown');
    const btn   = document.querySelector('.auth-submit-btn');

    function update() {
      if (seconds <= 0) {
        // Bloqueo expirado — recargar para habilitar el formulario
        location.reload();
        return;
      }
      const mins = Math.floor(seconds / 60);
      const secs = seconds % 60;
      el.textContent = mins + ' min ' + String(secs).padStart(2, '0') + ' seg';
      seconds--;
      setTimeout(update, 1000);
    }
    update();
  })();
  <?php endif; ?>

  // Enter en usuario → focus a contraseña
  document.getElementById('username').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      document.getElementById('password').focus();
    }
  });

  // Bloquear caracteres especiales en usuario (solo letras, números y _)
  document.getElementById('username').addEventListener('keypress', function(e) {
    if (!/[a-zA-Z0-9_]/.test(e.key)) e.preventDefault();
  });
  document.getElementById('username').addEventListener('paste', function(e) {
    e.preventDefault();
    const clean = (e.clipboardData || window.clipboardData).getData('text').replace(/[^a-zA-Z0-9_]/g, '');
    document.execCommand('insertText', false, clean);
  });

  // Bloquear espacios en contraseña
  document.getElementById('password').addEventListener('keypress', function(e) {
    if (e.key === ' ') e.preventDefault();
  });
})();
</script>
</body>
</html>
