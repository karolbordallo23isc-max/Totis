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
  <style>
    .mod-detail { display:none; margin-top:1rem; }
    .mod-detail.open { display:block; }
    .mod-row { cursor:pointer; transition:background .15s; }
    .mod-row:hover td { background:#f5f3ff; }
    .mod-row td:first-child { display:flex; align-items:center; gap:.5rem; }
    .mod-chevron { transition:transform .25s; display:inline-block; font-style:normal; }
    .mod-row.active .mod-chevron { transform:rotate(90deg); }
    .lesson-block { margin-bottom:1.25rem; }
    .lesson-block__title {
      font-weight:700; font-size:.9rem; color:var(--gray-700);
      padding:.4rem .75rem; background:var(--gray-100);
      border-radius:6px; margin-bottom:.5rem;
      display:flex; align-items:center; gap:.5rem;
    }
    .lesson-summary {
      font-size:.82rem; color:var(--gray-500);
      padding:.4rem .75rem; background:var(--gray-50);
      border-radius:6px; margin-top:.4rem;
    }
  </style>
</head>
<body>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="main-content">
<div class="page-container">

  <div class="admin-page-header">
    <div>
      <h1 class="page-title gradient-text">⚙️ Panel de Administración</h1>
      <p class="page-subtitle">Estadísticas, usuarios y gestión del curso</p>
    </div>
    <a href="<?= base_url('index.php?page=dashboard') ?>" class="btn btn-outline btn-sm">← Volver al inicio</a>
  </div>

  <?php if ($ok): ?><div class="alert alert-success mb-4"><?= e($ok) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-error mb-4"><?= e($error) ?></div><?php endif; ?>

  <!-- Stats generales -->
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
      <div class="admin-stat-card__label">Estudiantes</div>
    </div>
    <div class="admin-stat-card admin-stat-card--cyan">
      <div class="admin-stat-card__icon">🏆</div>
      <div class="admin-stat-card__value"><?= $stats['progresos'] ?></div>
      <div class="admin-stat-card__label">Ejercicios completados</div>
    </div>
  </div>

  <!-- Accesos rápidos -->
  <div class="admin-quick-grid" style="margin-bottom:2rem">
    <a href="<?= base_url('index.php?page=admin&action=modules') ?>" class="admin-quick-card">
      <span class="admin-quick-card__icon">📦</span>
      <div><strong>Gestionar Módulos</strong><p>Crear, editar y eliminar módulos</p></div>
      <span class="admin-quick-card__arrow">→</span>
    </a>
    <a href="<?= base_url('index.php?page=admin&action=users') ?>" class="admin-quick-card">
      <span class="admin-quick-card__icon">👥</span>
      <div><strong>Gestionar Usuarios</strong><p>Ver progreso y asignar roles</p></div>
      <span class="admin-quick-card__arrow">→</span>
    </a>
  </div>

  <!-- Progreso por módulo con acordeón -->
  <div class="card mb-4" id="stats-modulos">
    <div class="card-stripe card-stripe--blue"></div>
    <div class="card-body">
      <div class="card-section-title">
        <span class="card-section-icon card-section-icon--blue">📊</span>
        <div>
          <h3>Progreso por módulo</h3>
          <p class="text-sm text-gray">Haz clic en un módulo para ver el detalle de sus ejercicios</p>
        </div>
      </div>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Módulo</th>
              <th>Ejercicios</th>
              <th>Estudiantes</th>
              <th>Completaron el módulo</th>
              <th>Intentos registrados</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($statsByModule as $row):
              $mid = (int)$row['id_modulo'];
              $exs = $exercisesByModule[$mid] ?? [];
              $byLesson = [];
              foreach ($exs as $ex) $byLesson[$ex['leccion']][] = $ex;
              $totalIntentos = array_sum(array_column($exs, 'total_intentos'));
            ?>
            <tr class="mod-row" id="mod-row-<?= $mid ?>" onclick="toggleModule(<?= $mid ?>)">
              <td>
                <i class="mod-chevron">▶</i>
                <span><?= e($row['nombre']) ?></span>
              </td>
              <td><?= (int)$row['total_ejercicios'] ?></td>
              <td><?= (int)$row['total_usuarios'] ?></td>
              <td>
                <?php $completaron = (int)($row['usuarios_completaron'] ?? 0); ?>
                <?= $completaron ?> / <?= (int)$row['total_usuarios'] ?>
                <?php if ($row['total_usuarios'] > 0): ?>
                  <span style="color:var(--gray-400);font-size:.8rem">
                    (<?= round($completaron / $row['total_usuarios'] * 100) ?>%)
                  </span>
                <?php endif; ?>
              </td>
              <td><?= $totalIntentos > 0 ? $totalIntentos : '<span style="color:var(--gray-400)">—</span>' ?></td>
            </tr>
            <!-- Detalle expandible -->
            <tr id="mod-detail-row-<?= $mid ?>">
              <td colspan="5" style="padding:0;border:none">
                <div class="mod-detail" id="mod-detail-<?= $mid ?>">
                  <div style="padding:.75rem 1rem 1rem">
                    <?php if (empty($byLesson)): ?>
                      <p style="color:var(--gray-500);font-size:.9rem;padding:.5rem">Sin datos de intentos aún.</p>
                    <?php else: ?>
                    <?php foreach ($byLesson as $leccion => $lessonExs): ?>
                    <div class="lesson-block">
                      <div class="lesson-block__title">
                        📖 <?= e($leccion) ?>
                      </div>
                      <div class="admin-table-wrap">
                        <table class="admin-table" style="font-size:.85rem">
                          <thead>
                            <tr>
                              <th>Pregunta</th>
                              <th>Tipo</th>
                              <th>Intentos</th>
                              <th>Aciertos</th>
                              <th>Tasa</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($lessonExs as $ex):
                              $tasa  = (int)($ex['tasa_acierto'] ?? 0);
                              $color = $tasa >= 70 ? '#059669' : ($tasa >= 40 ? '#d97706' : '#dc2626');
                              $tipos = ['opcion_multiple'=>'Opción múltiple','verdadero_falso'=>'V/F','codigo'=>'Código'];
                            ?>
                            <tr>
                              <td style="max-width:260px;white-space:normal">
                                <?= e(mb_substr($ex['pregunta'], 0, 90)) ?><?= mb_strlen($ex['pregunta']) > 90 ? '…' : '' ?>
                              </td>
                              <td><?= $tipos[$ex['tipo']] ?? $ex['tipo'] ?></td>
                              <td><?= (int)$ex['total_intentos'] ?></td>
                              <td><?= (int)$ex['aciertos'] ?></td>
                              <td>
                                <?php if ($ex['total_intentos'] > 0): ?>
                                  <span style="color:<?= $color ?>;font-weight:700"><?= $tasa ?>%</span>
                                <?php else: ?>
                                  <span style="color:#9ca3af">—</span>
                                <?php endif; ?>
                              </td>
                            </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                      <?php
                        $ti = array_sum(array_column($lessonExs, 'total_intentos'));
                        $ta = array_sum(array_column($lessonExs, 'aciertos'));
                        $tg = $ti > 0 ? round($ta / $ti * 100) : 0;
                        $cg = $tg >= 70 ? '#059669' : ($tg >= 40 ? '#d97706' : '#dc2626');
                      ?>
                      <div class="lesson-summary">
                        <?= $ti ?> intentos · <?= $ta ?> aciertos ·
                        Tasa: <strong style="color:<?= $cg ?>"><?= $ti > 0 ? $tg.'%' : '—' ?></strong>
                      </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Ranking -->
  <?php if (!empty($ranking)): ?>
  <div class="card mb-4">
    <div class="card-stripe card-stripe--purple"></div>
    <div class="card-body">
      <div class="card-section-title">
        <span class="card-section-icon card-section-icon--purple">🏆</span>
        <h3>Ranking de estudiantes</h3>
      </div>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr><th>#</th><th>Estudiante</th><th>Ejercicios completados</th></tr>
          </thead>
          <tbody>
            <?php foreach ($ranking as $i => $u): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td>
                <a href="<?= base_url('index.php?page=admin&action=user_progress&user_id=' . (int)$u['id_usuario']) ?>">
                  <?= e($u['nombre']) ?>
                </a>
              </td>
              <td><?= (int)$u['ejercicios_completados'] ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php endif; ?>

</div>
</main>

<script>
function toggleModule(mid) {
  const row    = document.getElementById('mod-row-' + mid);
  const detail = document.getElementById('mod-detail-' + mid);
  const isOpen = detail.classList.contains('open');

  // Cerrar todos
  document.querySelectorAll('.mod-detail.open').forEach(d => d.classList.remove('open'));
  document.querySelectorAll('.mod-row.active').forEach(r => r.classList.remove('active'));

  if (!isOpen) {
    detail.classList.add('open');
    row.classList.add('active');
    // Scroll suave hacia el detalle
    setTimeout(() => {
      detail.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 50);
  }
}
</script>
<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
