<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($module['nombre']) ?> — Loopbook</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><defs><linearGradient id=%22g%22 x1=%220%22 y1=%220%22 x2=%2240%22 y2=%2240%22 gradientUnits=%22userSpaceOnUse%22><stop offset=%220%25%22 stop-color=%22%23cc0000%22/><stop offset=%2255%25%22 stop-color=%22%23ff2800%22/><stop offset=%22100%25%22 stop-color=%22%23ff6b00%22/></linearGradient></defs><rect width=%2240%22 height=%2240%22 rx=%2211%22 fill=%22url(%23g)%22/><text x=%2250%25%22 y=%2257%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2213%22 font-weight=%22700%22 fill=%22white%22>%3C/%3E</text></svg>">
  <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
  <style>
    /* ── Cabecera del módulo ─────────────────────────────── */
    .mod-hero {
      background: linear-gradient(135deg,#1e3a8a 0%,#3b82f6 50%,#7c3aed 100%);
      border-radius: 18px;
      padding: 1.75rem 2rem;
      margin-bottom: 1.5rem;
      color: #fff;
      position: relative;
      overflow: hidden;
    }
    .mod-hero::before {
      content:'';
      position:absolute; top:-40px; right:-40px;
      width:200px; height:200px;
      background:radial-gradient(circle,rgba(255,255,255,.1) 0%,transparent 70%);
      pointer-events:none;
    }
    .mod-hero__label {
      font-size:.72rem; font-weight:800; letter-spacing:1px;
      text-transform:uppercase; opacity:.75; margin-bottom:.35rem;
    }
    .mod-hero__title { font-size:1.5rem; font-weight:900; margin:0 0 .35rem; line-height:1.25; }
    .mod-hero__desc  { font-size:.88rem; opacity:.82; margin:0 0 1.25rem; line-height:1.5; }

    /* Barra de progreso en hero */
    .mod-hero__prow {
      display:flex; justify-content:space-between; align-items:center;
      margin-bottom:.4rem; font-size:.82rem; font-weight:600;
    }
    .mod-hero__pbar {
      height:8px; background:rgba(255,255,255,.25);
      border-radius:999px; overflow:hidden;
    }
    .mod-hero__pfill {
      height:100%; border-radius:999px;
      background:rgba(255,255,255,.85);
      transition:width .8s cubic-bezier(.22,.68,0,1.2);
    }
    .mod-hero__actions {
      display:flex; gap:.65rem; margin-top:1rem; flex-wrap:wrap;
    }
    .mod-hero__btn-reset {
      background:rgba(255,255,255,.18); color:#fff; border:1.5px solid rgba(255,255,255,.35);
      border-radius:9px; padding:.38rem .9rem;
      font-size:.78rem; font-weight:700; cursor:pointer;
      transition:background .15s;
    }
    .mod-hero__btn-reset:hover { background:rgba(255,255,255,.28); }

    /* ── Lista de lecciones ──────────────────────────────── */
    .lec-list { display:flex; flex-direction:column; gap:.85rem; }

    .lec-item {
      background:#fff;
      border-radius:14px;
      box-shadow:0 2px 10px rgba(0,0,0,.06);
      overflow:hidden;
      display:flex;
      align-items:stretch;
      transition:transform .2s cubic-bezier(.22,.68,0,1.2), box-shadow .2s;
    }
    .lec-item:hover { transform:translateX(4px); box-shadow:0 6px 20px rgba(0,0,0,.1); }
    .lec-item--done { border:1.5px solid #d1fae5; }
    .lec-item--done:hover { box-shadow:0 6px 20px rgba(16,185,129,.12); }
    .lec-item--locked { opacity:.68; filter:grayscale(.2); }
    .lec-item--locked:hover { transform:none; box-shadow:0 2px 10px rgba(0,0,0,.06); }

    /* Borde lateral de color */
    .lec-item__side {
      width:5px; flex-shrink:0;
    }
    .lec-item__side--done   { background:linear-gradient(180deg,#059669,#10b981); }
    .lec-item__side--active { background:linear-gradient(180deg,#4f46e5,#7c3aed); }
    .lec-item__side--locked { background:#e5e7eb; }

    /* Icono */
    .lec-item__ico {
      width:48px; height:48px; border-radius:12px; flex-shrink:0;
      display:flex; align-items:center; justify-content:center;
      margin:auto 1rem;
    }
    .lec-item__ico--done   { background:linear-gradient(135deg,#059669,#10b981); }
    .lec-item__ico--active { background:linear-gradient(135deg,#4f46e5,#7c3aed); }
    .lec-item__ico--locked { background:#f3f4f6; }

    /* Contenido */
    .lec-item__content {
      flex:1; padding:1rem .5rem 1rem 0; min-width:0;
    }
    .lec-item__num {
      font-size:.68rem; font-weight:800; letter-spacing:.8px;
      text-transform:uppercase; color:#9ca3af; display:block; margin-bottom:.2rem;
    }
    .lec-item__num--done   { color:#059669; }
    .lec-item__num--active { color:#4f46e5; }
    .lec-item__title {
      font-size:.95rem; font-weight:800; color:#111827;
      margin:0 0 .25rem; line-height:1.3;
    }
    .lec-item__desc {
      font-size:.78rem; color:#6b7280; line-height:1.4;
      display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
    }

    /* Acción */
    .lec-item__action {
      display:flex; align-items:center; padding:0 1.25rem 0 .75rem; flex-shrink:0;
    }
    .lec-btn {
      display:inline-flex; align-items:center; gap:.3rem;
      padding:.45rem 1.1rem; border-radius:9px;
      font-size:.82rem; font-weight:700; white-space:nowrap;
      text-decoration:none; transition:transform .15s, box-shadow .15s;
    }
    .lec-btn:hover { transform:translateY(-1px); text-decoration:none; }
    .lec-btn--start  { background:linear-gradient(135deg,#4f46e5,#7c3aed); color:#fff; box-shadow:0 3px 10px rgba(79,70,229,.3); }
    .lec-btn--review { background:#f3f4f6; color:#374151; border:1.5px solid #e5e7eb; }
    .lec-btn--review:hover { background:#ede9fe; border-color:#7c3aed; color:#4f46e5; }
    .lec-btn--edit   { background:linear-gradient(135deg,#059669,#10b981); color:#fff; box-shadow:0 3px 10px rgba(5,150,105,.3); }
    .lec-lock-msg { font-size:.75rem; color:#9ca3af; font-style:italic; }

    /* ── Banner módulo completado ────────────────────────── */
    .mod-done-banner {
      margin-top:2rem;
      background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 45%,#7c3aed 100%);
      border-radius:18px;
      padding:2.25rem 2rem;
      text-align:center;
      box-shadow:0 12px 40px rgba(30,58,138,.3);
      position:relative; overflow:hidden;
    }
    .mod-done-banner::before {
      content:'';position:absolute;top:-60px;left:-60px;
      width:200px;height:200px;
      background:radial-gradient(circle,rgba(255,255,255,.08) 0%,transparent 70%);
    }
    .mod-done-banner__trophy { font-size:3.5rem; display:block; margin-bottom:.5rem; }
    .mod-done-banner__title {
      font-size:1.5rem; font-weight:900; color:#fff; margin:0 0 .35rem;
    }
    .mod-done-banner__sub { font-size:.9rem; color:rgba(255,255,255,.8); margin:0 0 1.5rem; }
    .mod-done-banner__btn {
      display:inline-flex; align-items:center; gap:.45rem;
      background:#fff; color:#1e3a8a; font-weight:800; font-size:.95rem;
      padding:.75rem 1.75rem; border-radius:12px;
      text-decoration:none;
      box-shadow:0 4px 16px rgba(0,0,0,.18);
      transition:transform .15s, box-shadow .15s;
    }
    .mod-done-banner__btn:hover {
      transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.25);
      text-decoration:none; color:#1e3a8a;
    }

    html.dark .lec-item { background:var(--surface); }
    html.dark .lec-item__title { color:var(--text); }
    html.dark .lec-item__side--locked { background:var(--border); }
    html.dark .lec-item__ico--locked { background:var(--bg); }
    html.dark .lec-btn--review { background:var(--bg); border-color:var(--border); color:var(--text); }
  </style>
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<main class="main-content">
<div class="page-container page-container--narrow">

  <a href="<?= base_url('index.php?page=dashboard') ?>"
     class="btn btn-outline btn-back"
     style="margin-bottom:1.25rem;display:inline-flex;gap:.4rem;align-items:center">
    ← Volver a Módulos
  </a>

  <!-- Hero del módulo -->
  <div class="mod-hero">
    <p class="mod-hero__label">Módulo <?= e($module['orden'] ?? '') ?></p>
    <h1 class="mod-hero__title"><?= e($module['nombre']) ?></h1>
    <p class="mod-hero__desc"><?= e($module['descripcion']) ?></p>

    <div class="mod-hero__prow">
      <span>Ejercicios completados</span>
      <strong><?= $completedEx ?> / <?= $totalEx ?></strong>
    </div>
    <div class="mod-hero__pbar">
      <div class="mod-hero__pfill" style="width:<?= $progressPct ?>%"></div>
    </div>

    <?php if ($completedEx > 0): ?>
    <div class="mod-hero__actions">
      <button type="button" class="mod-hero__btn-reset"
              onclick="resetModule(<?= (int)$module['id_modulo'] ?>)">
        Reiniciar módulo
      </button>
    </div>
    <?php endif; ?>
  </div>

  <!-- Lista de lecciones -->
  <div class="lec-list">
    <?php foreach ($lessons as $i => $lesson):
      $lessonUnlocked = $i === 0 || $lessons[$i - 1]['completed'];

      if ($lesson['completed']) {
        $sideClass = 'lec-item__side--done';
        $icoClass  = 'lec-item__ico--done';
        $numClass  = 'lec-item__num--done';
        $itemExtra = 'lec-item--done';
      } elseif ($lessonUnlocked) {
        $sideClass = 'lec-item__side--active';
        $icoClass  = 'lec-item__ico--active';
        $numClass  = 'lec-item__num--active';
        $itemExtra = '';
      } else {
        $sideClass = 'lec-item__side--locked';
        $icoClass  = 'lec-item__ico--locked';
        $numClass  = '';
        $itemExtra = 'lec-item--locked';
      }
    ?>
    <div class="lec-item <?= $itemExtra ?>">
      <div class="lec-item__side <?= $sideClass ?>"></div>

      <div class="lec-item__ico <?= $icoClass ?>">
        <?php if ($lesson['completed']): ?>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        <?php elseif ($lessonUnlocked): ?>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        <?php else: ?>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <?php endif; ?>
      </div>

      <div class="lec-item__content">
        <span class="lec-item__num <?= $numClass ?>">LECCION <?= $i + 1 ?></span>
        <h3 class="lec-item__title"><?= e($lesson['title']) ?></h3>
        <p class="lec-item__desc"><?= e($lesson['content']) ?></p>
      </div>

      <div class="lec-item__action">
        <?php if (!empty($_SESSION['is_admin'])): ?>
          <a href="<?= base_url('index.php?page=admin&action=lesson_edit&id=' . $lesson['id']) ?>"
             class="lec-btn lec-btn--edit">Editar</a>
        <?php elseif ($lessonUnlocked): ?>
          <?php if ($lesson['completed']): ?>
            <a href="<?= base_url('index.php?page=lesson&module_id=' . $module['id_modulo'] . '&lesson_id=' . $lesson['id']) ?>"
               class="lec-btn lec-btn--review">Revisar</a>
          <?php else: ?>
            <a href="<?= base_url('index.php?page=lesson&module_id=' . $module['id_modulo'] . '&lesson_id=' . $lesson['id']) ?>"
               class="lec-btn lec-btn--start">Comenzar</a>
          <?php endif; ?>
        <?php else: ?>
          <span class="lec-lock-msg">Completa la leccion <?= $i ?> primero</span>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Banner módulo completado -->
  <?php if ($progressPct === 100 && $totalEx > 0): ?>
  <div class="mod-done-banner">
    <span class="mod-done-banner__trophy">&#127942;</span>
    <h2 class="mod-done-banner__title">Módulo Completado</h2>
    <p class="mod-done-banner__sub">
      Completaste todos los ejercicios de <strong><?= e($module['nombre']) ?></strong>
    </p>
    <?php if ($nextModule): ?>
      <a href="<?= base_url('index.php?page=module&id=' . (int)$nextModule['id_modulo']) ?>"
         class="mod-done-banner__btn">
        Ir al siguiente módulo →
      </a>
    <?php else: ?>
      <a href="<?= base_url('index.php?page=dashboard') ?>"
         class="mod-done-banner__btn">
        Ver todos los módulos →
      </a>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</div>
</main>

<script src="<?= base_url('js/app.js') ?>"></script>
<script>
  const RESET_PROGRESS_URL = '<?= base_url('api/reset_progress.php') ?>';

  function resetModule(moduleId) {
    if (!confirm('Reiniciar el módulo borrará todo tu progreso. Continuar?')) return;
    const fd = new FormData();
    fd.append('type',      'module');
    fd.append('module_id', moduleId);
    fetch(RESET_PROGRESS_URL, { method:'POST', body:fd })
      .then(r => r.json())
      .then(d => { if (d.success) location.reload(); else alert('No se pudo reiniciar.'); })
      .catch(() => alert('Error de conexión.'));
  }
</script>
</body>
</html>
