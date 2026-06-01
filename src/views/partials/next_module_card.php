<?php
/**
 * Partial: tarjeta de siguiente módulo.
 * Variables esperadas:
 *   $nextModule — array del siguiente módulo, o false si es el último.
 *   $titleText  — texto del título.
 */
?>
<div class="next-module-banner">
  <div class="next-module-banner__confetti">🎉</div>

  <div class="next-module-banner__body">
    <div class="next-module-banner__check">✅</div>
    <div class="next-module-banner__text">
      <h3 class="next-module-banner__title"><?= e($titleText) ?></h3>
      <?php if ($nextModule): ?>
        <p class="next-module-banner__sub">
          Siguiente módulo: <strong><?= e($nextModule['nombre']) ?></strong>
        </p>
      <?php else: ?>
        <p class="next-module-banner__sub">
          🏆 ¡Has completado todos los módulos del curso!
        </p>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($nextModule): ?>
    <a href="<?= base_url('index.php?page=module&id=' . (int)$nextModule['id_modulo']) ?>"
       class="btn next-module-banner__btn">
      Ir al siguiente módulo →
    </a>
  <?php else: ?>
    <a href="<?= base_url('index.php?page=dashboard') ?>"
       class="btn next-module-banner__btn">
      Ver todos los módulos →
    </a>
  <?php endif; ?>
</div>
