'use strict';

/**
 * app.js — Lógica del cliente para Loopbook
 *
 * Responsabilidades:
 *  1. Modo oscuro        — aplica/quita la clase `dark` en <html> y persiste en localStorage
 *  2. Selección de opción — resalta visualmente la opción elegida en un ejercicio
 *  3. Verificación de respuesta — envía la respuesta al servidor (AJAX) y actualiza la interfaz
 *  4. Pantalla de celebración — muestra un overlay al completar todos los ejercicios del módulo
 */

/* Contador de respuestas correctas en la sesión actual de la lección */
let correctCount = 0;

/* ══════════════════════════════════════════════════════════
   SONIDOS WEB AUDIO API
   Genera sonidos en tiempo real sin archivos externos.
   ══════════════════════════════════════════════════════════ */
const AudioCtx = window.AudioContext || window.webkitAudioContext;
let _audioCtx = null;
function getAudioCtx() {
  if (!_audioCtx) _audioCtx = new AudioCtx();
  return _audioCtx;
}
function playTone(freq, type, startTime, duration, gainVal, ctx) {
  const osc = ctx.createOscillator();
  const gain = ctx.createGain();
  osc.connect(gain); gain.connect(ctx.destination);
  osc.type = type;
  osc.frequency.setValueAtTime(freq, startTime);
  gain.gain.setValueAtTime(gainVal, startTime);
  gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration);
  osc.start(startTime); osc.stop(startTime + duration);
}
function playCorrect() {
  try { const ctx = getAudioCtx(), now = ctx.currentTime;
    [523.25, 659.25, 783.99].forEach((f, i) => playTone(f, 'sine', now + i * 0.08, 0.3, 0.18, ctx)); } catch(e) {}
}
function playWrong() {
  try { const ctx = getAudioCtx(), now = ctx.currentTime;
    [300, 250, 200].forEach((f, i) => playTone(f, 'sawtooth', now + i * 0.1, 0.2, 0.12, ctx)); } catch(e) {}
}
function playFanfare() {
  try { const ctx = getAudioCtx(), now = ctx.currentTime;
    [523, 659, 784, 1047, 784, 1047].forEach((f, i) => playTone(f, 'sine', now + i * 0.12, 0.25, 0.2, ctx)); } catch(e) {}
}
function playClick() {
  try { const ctx = getAudioCtx(); playTone(800, 'sine', ctx.currentTime, 0.05, 0.08, ctx); } catch(e) {}
}

/* ══════════════════════════════════════════════════════════
   CONFETTI
   Divs animados que caen desde arriba. Sin librerías.
   ══════════════════════════════════════════════════════════ */
function launchConfetti() {
  const colors = ['#1e3a8a','#7c3aed','#10b981','#f59e0b','#ef4444','#06b6d4','#a855f7'];
  if (!document.getElementById('confetti-style')) {
    const s = document.createElement('style');
    s.id = 'confetti-style';
    s.textContent = '@keyframes confettiFall{0%{transform:translateY(0) rotate(0deg);opacity:1}100%{transform:translateY(100vh) rotate(720deg);opacity:0}}';
    document.head.appendChild(s);
  }
  function burst(count) {
    for (let i = 0; i < count; i++) {
      const el = document.createElement('div');
      el.style.cssText = `position:fixed;top:-10px;left:${Math.random()*100}vw;width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;background:${colors[Math.floor(Math.random()*colors.length)]};border-radius:${Math.random()>.5?'50%':'2px'};opacity:1;z-index:9999;animation:confettiFall ${1.5+Math.random()}s ease-in forwards;`;
      document.body.appendChild(el);
      el.addEventListener('animationend', () => el.remove());
    }
  }
  burst(80); setTimeout(() => burst(60), 600);
}

/* ══════════════════════════════════════════════════════════
   TOOLTIPS AJAX — DASHBOARD
   ══════════════════════════════════════════════════════════ */
function initTooltips() {
  if (typeof MODULE_PREVIEW_URL === 'undefined') return;
  document.querySelectorAll('[data-module-id]').forEach(card => {
    let tooltip = null, timer = null;
    card.addEventListener('mouseenter', () => {
      timer = setTimeout(() => {
        fetch(`${MODULE_PREVIEW_URL}?id=${card.dataset.moduleId}`)
          .then(r => r.json()).then(data => {
            if (tooltip) tooltip.remove();
            tooltip = document.createElement('div');
            tooltip.className = 'module-tooltip';
            tooltip.innerHTML = `<strong>${data.moduleName}</strong>` +
              data.lessons.map(l => `<div class="module-tooltip__lesson"><span>${l.completed?'✅':'⬜'}</span><span>${l.title}</span></div>`).join('');
            card.style.position = 'relative';
            card.appendChild(tooltip);
          }).catch(() => {});
      }, 300);
    });
    card.addEventListener('mouseleave', () => {
      clearTimeout(timer);
      if (tooltip) { tooltip.remove(); tooltip = null; }
    });
  });
}

/* ══════════════════════════════════════════════════════════
   1. MODO OSCURO
   Inyecta el botón 🌙/☀️ en el header (solo pantallas grandes).
   En móvil el toggle está dentro del menú desplegable (header.php).
   Al hacer clic alterna la clase `dark` en <html> y guarda
   la preferencia en localStorage con la clave 'lb-dark'.
   ══════════════════════════════════════════════════════════ */
function initDarkMode() {
  /* Restaurar preferencia guardada antes de pintar la página */
  const saved = localStorage.getItem('lb-dark');
  if (saved === '1') document.documentElement.classList.add('dark');

  if (window.innerWidth <= 640) return;

  const headerRight = document.querySelector('.header-right');
  if (!headerRight) return;

  /* Crear botón y colocarlo al inicio del área derecha del header */
  const btn = document.createElement('button');
  btn.className = 'btn btn-outline btn-sm dark-toggle';
  btn.setAttribute('aria-label', 'Cambiar tema');
  btn.innerHTML = document.documentElement.classList.contains('dark') ? '☀️' : '🌙';

  btn.addEventListener('click', () => {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('lb-dark', isDark ? '1' : '0');
    /* Actualizar ícono según el tema activo */
    btn.innerHTML = isDark ? '☀️' : '🌙';
  });

  headerRight.insertBefore(btn, headerRight.firstChild);
}

/* ══════════════════════════════════════════════════════════
   2. SELECCIONAR OPCIÓN
   Llamado desde onclick en cada botón de opción (lesson.php).
   Visualmente:
   - Quita la clase `selected` y `wrong` de todas las opciones
   - Agrega `selected` al botón pulsado (borde azul + fondo claro)
   - Muestra el botón "Verificar Respuesta" que estaba oculto
   - Si había un mensaje de error previo, lo oculta para reintentar
   ══════════════════════════════════════════════════════════ */
function selectOption(btn) {
  const exercise = btn.closest('.exercise');

  /* Limpiar selección anterior */
  exercise.querySelectorAll('.option-btn').forEach(b => {
    b.classList.remove('selected', 'wrong');
  });

  /* Marcar la opción elegida */
  btn.classList.add('selected');
  playClick();

  /* Mostrar el botón de verificar */
  const checkBtn = exercise.querySelector('.check-btn');
  if (checkBtn) checkBtn.classList.remove('hidden');

  /* Ocultar mensaje de error anterior para permitir reintento */
  const feedback = exercise.querySelector('.exercise__feedback');
  if (feedback && feedback.classList.contains('exercise__feedback--wrong')) {
    feedback.classList.add('hidden');
    feedback.classList.remove('exercise__feedback--wrong');
  }
}

/* ══════════════════════════════════════════════════════════
   3. VERIFICAR RESPUESTA
   Llamado desde onclick en el botón "Verificar Respuesta".
   Flujo visual:
   - Deshabilita el botón y muestra "Verificando…" mientras espera
   - Si CORRECTO:
       · Colorea en verde la opción correcta (.correct)
       · Oculta el botón de verificar
       · Muestra el mensaje en verde (.exercise__feedback--correct)
       · Muestra la explicación si el servidor la envía
       · Si es el último ejercicio de la última lección → celebración
   - Si INCORRECTO:
       · Colorea en rojo la opción elegida (.wrong)
       · Muestra el mensaje en naranja (.exercise__feedback--wrong)
       · Reactiva el botón para reintentar
   - Si hay error de red: reactiva el botón y muestra alerta
   ══════════════════════════════════════════════════════════ */
function checkAnswer(checkBtn, exerciseId) {
  const exercise    = checkBtn.closest('.exercise');
  const selectedBtn = exercise.querySelector('.option-btn.selected');
  if (!selectedBtn) return;

  const selectedOptionId = parseInt(selectedBtn.dataset.optionId, 10);

  /* Estado de carga */
  checkBtn.disabled = true;
  checkBtn.textContent = 'Verificando…';

  const formData = new FormData();
  formData.append('exercise_id',        exerciseId);
  formData.append('selected_option_id', selectedOptionId);

  fetch(CHECK_ANSWER_URL, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
      if (!data.success) {
        /* Error del servidor — reactivar para reintentar */
        checkBtn.disabled = false;
        checkBtn.textContent = 'Verificar Respuesta';
        return;
      }

      const isCorrect       = data.correct;
      const correctOptionId = data.correctOptionId;

      if (isCorrect) {
        /* ── Respuesta correcta ── */

        /* Bloquear todas las opciones y marcar la correcta en verde */
        exercise.querySelectorAll('.option-btn').forEach(btn => {
          btn.disabled = true;
          if (parseInt(btn.dataset.optionId, 10) === correctOptionId) {
            btn.classList.add('correct');
          }
        });

        checkBtn.classList.add('hidden');
        playCorrect();

        /* Mostrar mensaje de éxito en verde */
        const feedback = exercise.querySelector('.exercise__feedback');
        feedback.classList.remove('hidden', 'exercise__feedback--wrong');
        feedback.classList.add('exercise__feedback--correct');
        feedback.textContent = data.feedback || '¡Correcto! 🎉';

        /* Mostrar explicación adicional si el servidor la envía */
        const explanation = exercise.querySelector('.exercise__explanation');
        if (explanation && data.explanation) {
          explanation.textContent = data.explanation;
          explanation.classList.remove('hidden');
        }

        correctCount++;

        /* Mostrar celebración si se completaron todos los ejercicios
           de la última lección del módulo */
        if (typeof IS_LAST_LESSON !== 'undefined' && IS_LAST_LESSON &&
            typeof TOTAL_EXERCISES !== 'undefined' && correctCount >= TOTAL_EXERCISES) {
          setTimeout(() => {
            if (typeof MODULE_NAME !== 'undefined') {
              showCompletionScreen(MODULE_NAME);
            }
          }, 800);
        }

      } else {
        /* ── Respuesta incorrecta ── */

        /* Marcar en rojo solo la opción elegida */
        exercise.querySelectorAll('.option-btn').forEach(btn => {
          if (parseInt(btn.dataset.optionId, 10) === selectedOptionId) {
            btn.classList.add('wrong');
          }
        });

        /* Mostrar mensaje de error en naranja */
        const feedback = exercise.querySelector('.exercise__feedback');
        feedback.classList.remove('hidden', 'exercise__feedback--correct');
        feedback.classList.add('exercise__feedback--wrong');
        feedback.textContent = data.feedback || '¡Respuesta incorrecta! Selecciona otra opción. 💡';
        playWrong();

        /* Reactivar botón para que el usuario pueda reintentar */
        checkBtn.disabled = false;
        checkBtn.textContent = 'Verificar Respuesta';
        checkBtn.classList.remove('hidden');
      }
    })
    .catch(() => {
      /* Error de red — reactivar y avisar */
      checkBtn.disabled = false;
      checkBtn.textContent = 'Verificar Respuesta';
      alert('Error de conexión. Intenta de nuevo.');
    });
}

/* ══════════════════════════════════════════════════════════
   4. PANTALLA DE CELEBRACIÓN
   Se muestra cuando el usuario completa todos los ejercicios
   de la última lección de un módulo.
   Crea un overlay oscuro con una tarjeta centrada que contiene:
   trofeo 🏆, título, nombre del módulo, estrellas y botón
   para cerrar el overlay y continuar navegando.
   ══════════════════════════════════════════════════════════ */
function showCompletionScreen(moduleName) {
  playFanfare();
  launchConfetti();
  const overlay = document.createElement('div');
  overlay.className = 'completion-overlay';
  overlay.innerHTML = `
    <div class="completion-card">
      <div class="completion-icon">🏆</div>
      <h2 class="completion-title">¡Módulo Completado!</h2>
      <p class="completion-module">${moduleName}</p>
      <div class="completion-stars">⭐⭐⭐</div>
      <p class="completion-msg">Respondiste todos los ejercicios correctamente</p>
      <button class="btn btn-primary completion-btn" onclick="this.closest('.completion-overlay').remove()">
        ¡Continuar! →
      </button>
    </div>`;
  document.body.appendChild(overlay);
}

/* Exponer para que pueda ser llamada desde lesson.php */
window.showCompletionScreen = showCompletionScreen;

/* ══════════════════════════════════════════════════════════
   INICIO — Punto de entrada al cargar el DOM
   ══════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  initDarkMode();
  initTooltips();
});
