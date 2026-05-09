<?php
/**
 * Partial: tarjeta de siguiente módulo.
 * Variables esperadas del contexto:
 *   $nextModule — array del siguiente módulo, o null si es el último del curso.
 *   $titleText  — texto del título (ej: "¡Módulo completado!" o "¡Has terminado este módulo!").
 */
?>
<div class="card mt-4" style="border: 2px solid #10b981;">
  <div class="card-body next-module-card" style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
    <div>
      <p style="font-weight:700; font-size:1.05rem; margin:0;">🎉 <?= e($titleText) ?></p>
      <?php if ($nextModule): ?>
        <p class="text-sm text-gray" style="margin:0.25rem 0 0;">Siguiente: <?= e($nextModule['nombre']) ?></p>
      <?php else: ?>
        <p class="text-sm text-gray" style="margin:0.25rem 0 0;">Has completado todos los módulos del curso.</p>
      <?php endif; ?>
    </div>
    <?php if ($nextModule): ?>
      <a href="<?= base_url('index.php?page=module&id=' . (int)$nextModule['id_modulo']) ?>"
         class="btn btn-gradient btn-gradient--purple-pink">
        Siguiente módulo →
      </a>
    <?php else: ?>
      <a href="<?= base_url('index.php?page=dashboard') ?>"
         class="btn btn-gradient btn-gradient--purple-pink">
        Ver todos los módulos →
      </a>
    <?php endif; ?>
  </div>
</div>
