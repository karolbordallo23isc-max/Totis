<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estadísticas — <?= e($moduleRow['nombre']) ?> — Admin Loopbook</title>
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
      <span class="admin-breadcrumb">Estadísticas por módulo</span>
      <span class="admin-breadcrumb-sep">›</span>
      <h1 class="page-title gradient-text" style="display:inline"><?= e($moduleRow['nombre']) ?></h1>
    </div>
    <a href="<?= base_url('index.php?page=admin') ?>" class="btn btn-outline btn-sm">← Volver al panel</a>
  </div>

  <?php
  // Agrupar ejercicios por lección
  $byLesson = [];
  foreach ($exercises as $ex) {
      $byLesson[$ex['leccion']][] = $ex;
  }
  ?>

  <?php if (empty($exercises)): ?>
    <div class="card">
      <div class="card-body" style="text-align:center;padding:2rem">
        <p style="font-size:2rem">📊</p>
        <p>Aún no hay intentos registrados en este módulo.</p>
      </div>
    </div>
  <?php else: ?>

  <?php foreach ($byLesson as $leccion => $exs): ?>
  <div class="card mb-4">
    <div class="card-stripe card-stripe--purple"></div>
    <div class="card-body">
      <div class="card-section-title">
        <span class="card-section-icon card-section-icon--purple">📖</span>
        <h3><?= e($leccion) ?></h3>
      </div>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Pregunta</th>
              <th>Tipo</th>
              <th>Intentos</th>
              <th>Aciertos</th>
              <th>Tasa de acierto</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($exs as $ex): ?>
            <tr>
              <td style="max-width:300px;white-space:normal">
                <?= e(mb_substr($ex['pregunta'], 0, 100)) ?><?= mb_strlen($ex['pregunta']) > 100 ? '…' : '' ?>
              </td>
              <td>
                <?php
                  $tipos = ['opcion_multiple' => '🔘 Opción múltiple', 'verdadero_falso' => '✅ V/F', 'codigo' => '🖥️ Código'];
                  echo $tipos[$ex['tipo']] ?? $ex['tipo'];
                ?>
              </td>
              <td><?= (int)$ex['total_intentos'] ?></td>
              <td><?= (int)$ex['aciertos'] ?></td>
              <td>
                <?php
                  $tasa = (int)($ex['tasa_acierto'] ?? 0);
                  $color = $tasa >= 70 ? '#059669' : ($tasa >= 40 ? '#d97706' : '#dc2626');
                  $label = $ex['total_intentos'] == 0 ? '—' : $tasa . '%';
                ?>
                <?php if ($ex['total_intentos'] > 0): ?>
                <div style="display:flex;align-items:center;gap:.5rem">
                  <div class="progress-bar" style="width:80px;flex-shrink:0">
                    <div class="progress-bar__fill" style="width:<?= $tasa ?>%;background:<?= $color ?>"></div>
                  </div>
                  <span style="color:<?= $color ?>;font-weight:700"><?= $tasa ?>%</span>
                </div>
                <?php else: ?>
                  <span style="color:#9ca3af">Sin intentos</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php
        // Resumen de la lección
        $totalIntentos = array_sum(array_column($exs, 'total_intentos'));
        $totalAciertos = array_sum(array_column($exs, 'aciertos'));
        $tasaGeneral   = $totalIntentos > 0 ? round($totalAciertos / $totalIntentos * 100) : 0;
      ?>
      <div style="margin-top:.75rem;padding:.6rem 1rem;background:var(--gray-50);border-radius:8px;font-size:.88rem;color:var(--gray-600)">
        Resumen lección: <strong><?= $totalIntentos ?></strong> intentos totales ·
        <strong><?= $totalAciertos ?></strong> aciertos ·
        Tasa general: <strong style="color:<?= $tasaGeneral >= 70 ? '#059669' : ($tasaGeneral >= 40 ? '#d97706' : '#dc2626') ?>"><?= $totalIntentos > 0 ? $tasaGeneral . '%' : '—' ?></strong>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <?php endif; ?>

</div>
</main>
<script src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>
