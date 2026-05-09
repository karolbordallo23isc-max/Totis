<?php
$currentPage = $_GET['page'] ?? '';
$showHeader  = !in_array($currentPage, ['login', 'register', '']) && !empty($_SESSION['user_id']);
?>
<?php if ($showHeader): ?>
<header class="header">
  <div class="header-inner">

    <a href="<?= base_url('index.php?page=dashboard') ?>" class="logo-link">
      <svg class="logo-icon-svg" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <defs>
          <linearGradient id="hg" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
            <stop offset="0%"   stop-color="#cc0000"/>
            <stop offset="55%"  stop-color="#ff2800"/>
            <stop offset="100%" stop-color="#ff6b00"/>
          </linearGradient>
        </defs>
        <rect width="40" height="40" rx="11" fill="url(#hg)"/>
        <rect width="40" height="40" rx="11" fill="none" stroke="rgba(255,255,255,.25)" stroke-width="1.2"/>
        <text x="50%" y="56%" dominant-baseline="middle" text-anchor="middle"
              font-family="monospace" font-size="13" font-weight="700" fill="white">&lt;/&gt;</text>
        <path d="M28 7 Q36 7 36 15" stroke="white" stroke-width="1.5" stroke-linecap="round" fill="none" opacity=".45"/>
        <path d="M12 33 Q4 33 4 25"  stroke="white" stroke-width="1.5" stroke-linecap="round" fill="none" opacity=".45"/>
      </svg>
      <div class="logo-wordmark">
        <span class="logo-wordmark__name">Loopbook</span>
      </div>
    </a>

    <nav class="header-nav">
      <a href="<?= base_url('index.php?page=dashboard') ?>"
         class="header-nav__link <?= $currentPage === 'dashboard' ? 'header-nav__link--active' : '' ?>">
        🏠 Inicio
      </a>
      <a href="<?= base_url('index.php?page=profile') ?>"
         class="header-nav__link header-nav__link--profile <?= $currentPage === 'profile' ? 'header-nav__link--active' : '' ?>">
        <?= e($_SESSION['avatar'] ?? '👤') ?> <?= e($_SESSION['nombre'] ?? $_SESSION['username'] ?? '') ?>
      </a>
    </nav>

    <div class="header-right">
      <a href="<?= base_url('index.php?page=logout') ?>" class="btn btn-outline btn-sm btn-danger hdr-desktop-only">⏻ Salir</a>

      <div class="mob-menu hdr-mobile-only" id="mobMenu">
        <button class="mob-menu__btn" onclick="toggleMobMenu()" aria-label="Menú">
          <?= e($_SESSION['avatar'] ?? '👤') ?> ▾
        </button>
        <div class="mob-menu__dropdown">
          <a href="<?= base_url('index.php?page=profile') ?>" class="mob-menu__item">👤 Mi Perfil</a>
          <button class="mob-menu__item" onclick="toggleDark(this)">
            <span class="dark-icon">🌙</span> <span class="dark-lbl">Modo oscuro</span>
          </button>
          <div class="mob-menu__divider"></div>
          <a href="<?= base_url('index.php?page=logout') ?>" class="mob-menu__item mob-menu__item--danger">⏻ Salir</a>
        </div>
      </div>
    </div>
  </div>
</header>

<script>
function toggleMobMenu() {
  document.getElementById('mobMenu').classList.toggle('mob-menu--open');
}
document.addEventListener('click', e => {
  const m = document.getElementById('mobMenu');
  if (m && !m.contains(e.target)) m.classList.remove('mob-menu--open');
});
function toggleDark(btn) {
  const isDark = document.documentElement.classList.toggle('dark');
  localStorage.setItem('lb-dark', isDark ? '1' : '0');
  document.querySelector('.dark-icon').textContent = isDark ? '☀️' : '🌙';
  document.querySelector('.dark-lbl').textContent  = isDark ? 'Modo claro' : 'Modo oscuro';
}
document.addEventListener('DOMContentLoaded', () => {
  if (localStorage.getItem('lb-dark') === '1') {
    document.documentElement.classList.add('dark');
    const di = document.querySelector('.dark-icon');
    const dl = document.querySelector('.dark-lbl');
    if (di) di.textContent = '☀️';
    if (dl) dl.textContent = 'Modo claro';
  }
});
</script>
<?php endif; ?>
