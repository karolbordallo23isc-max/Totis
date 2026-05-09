'use strict';

let correctCount = 0;

/* ══════════════════════════════════════════════════════════
   1. SONIDOS (Web Audio API — sin archivos externos)
   ══════════════════════════════════════════════════════════ */
const AudioCtx = window.AudioContext || window.webkitAudioContext;
let _ctx = null;
function getAudioCtx() {
  if (!_ctx) _ctx = new AudioCtx();
  return _ctx;
}

function playSound(type) {
  try {
    const ctx = getAudioCtx();
    const master = ctx.createGain();
    master.gain.value = 0.18;
    master.connect(ctx.destination);

    if (type === 'correct') {
      // Acorde mayor ascendente — satisfactorio
      [[523.25, 0], [659.25, 0.1], [783.99, 0.2], [1046.5, 0.32]].forEach(([freq, delay]) => {
        const o = ctx.createOscillator();
        const g = ctx.createGain();
        o.type = 'sine';
        o.frequency.value = freq;
        g.gain.setValueAtTime(0, ctx.currentTime + delay);
        g.gain.linearRampToValueAtTime(0.5, ctx.currentTime + delay + 0.04);
        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.35);
        o.connect(g); g.connect(master);
        o.start(ctx.currentTime + delay);
        o.stop(ctx.currentTime + delay + 0.4);
      });
    } else if (type === 'wrong') {
      // Dos tonos descendentes — error suave
      [[300, 0], [220, 0.15]].forEach(([freq, delay]) => {
        const o = ctx.createOscillator();
        const g = ctx.createGain();
        o.type = 'sawtooth';
        o.frequency.value = freq;
        g.gain.setValueAtTime(0.3, ctx.currentTime + delay);
        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.25);
        o.connect(g); g.connect(master);
        o.start(ctx.currentTime + delay);
        o.stop(ctx.currentTime + delay + 0.3);
      });
    } else if (type === 'complete') {
      // Fanfarria corta — módulo completado
      [[523, 0], [659, 0.12], [784, 0.24], [1047, 0.38], [1319, 0.52]].forEach(([freq, delay]) => {
        const o = ctx.createOscillator();
        const g = ctx.createGain();
        o.type = 'triangle';
        o.frequency.value = freq;
        g.gain.setValueAtTime(0, ctx.currentTime + delay);
        g.gain.linearRampToValueAtTime(0.6, ctx.currentTime + delay + 0.05);
        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.5);
        o.connect(g); g.connect(master);
        o.start(ctx.currentTime + delay);
        o.stop(ctx.currentTime + delay + 0.55);
      });
    } else if (type === 'select') {
      // Click suave al seleccionar opción
      const o = ctx.createOscillator();
      const g = ctx.createGain();
      o.type = 'sine';
      o.frequency.value = 880;
      g.gain.setValueAtTime(0.15, ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.08);
      o.connect(g); g.connect(master);
      o.start(); o.stop(ctx.currentTime + 0.1);
    }
  } catch(e) { /* silencioso si el navegador bloquea */ }
}

/* ══════════════════════════════════════════════════════════
   2. MODO OSCURO
   ══════════════════════════════════════════════════════════ */
function initDarkMode() {
  const saved = localStorage.getItem('lb-dark');
  if (saved === '1') document.documentElement.classList.add('dark');
  // Solo inyectar la luna en desktop — en móvil ya está dentro del menú desplegable
  if (window.innerWidth <= 640) return;
  const headerRight = document.querySelector('.header-right');
  if (!headerRight) return;
  const btn = document.createElement('button');
  btn.className = 'btn btn-outline btn-sm dark-toggle';
  btn.setAttribute('aria-label', 'Cambiar tema');
  btn.innerHTML = document.documentElement.classList.contains('dark') ? '☀️' : '🌙';
  btn.addEventListener('click', () => {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('lb-dark', isDark ? '1' : '0');
    btn.innerHTML = isDark ? '☀️' : '🌙';
    playSound('select');
  });
  headerRight.insertBefore(btn, headerRight.firstChild);
}

/* ══════════════════════════════════════════════════════════
   3. TOOLTIPS EN MÓDULOS (preview de lecciones)
   ══════════════════════════════════════════════════════════ */
function initModuleTooltips() {
  document.querySelectorAll('.module-card').forEach(card => {
    const link = card.querySelector('a.btn');
    if (!link) return;
    const href = link.getAttribute('href');
    if (!href) return;

    // Crear tooltip
    const tip = document.createElement('div');
    tip.className = 'module-tooltip';
    tip.innerHTML = '<div class="module-tooltip__loading">Cargando…</div>';
    card.style.position = 'relative';
    card.appendChild(tip);

    let loaded = false;
    let showTimer, hideTimer;

    card.addEventListener('mouseenter', () => {
      clearTimeout(hideTimer);
      showTimer = setTimeout(() => {
        tip.classList.add('module-tooltip--visible');
        if (!loaded) {
          loaded = true;
          // Extraer module id del href
          const match = href.match(/id=(\d+)/);
          if (!match) return;
          fetch(`${href.split('?')[0].replace('module.php','api/module_preview.php')}?id=${match[1]}`)
            .then(r => r.json())
            .then(data => {
              if (!data.lessons) return;
              tip.innerHTML = `
                <div class="module-tooltip__title">${data.moduleName}</div>
                <ul class="module-tooltip__list">
                  ${data.lessons.map((l, i) => `
                    <li class="${l.completed ? 'done' : ''}">
                      ${l.completed ? '✅' : `<span class="tip-num">${i+1}</span>`}
                      ${l.title}
                    </li>`).join('')}
                </ul>`;
            })
            .catch(() => { tip.innerHTML = '<div class="module-tooltip__loading">Vista previa no disponible</div>'; });
        }
      }, 350);
    });

    card.addEventListener('mouseleave', () => {
      clearTimeout(showTimer);
      hideTimer = setTimeout(() => tip.classList.remove('module-tooltip--visible'), 150);
    });
  });
}

/* ══════════════════════════════════════════════════════════
   4. PANTALLA DE CELEBRACIÓN AL COMPLETAR MÓDULO
   ══════════════════════════════════════════════════════════ */
function showCompletionScreen(moduleName) {
  playSound('complete');
  launchConfetti(['#ff2800','#ff6b00','#ffcc00','#fff','#ff9500'], 120);

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

  // Segunda ola de confetti
  setTimeout(() => launchConfetti(['#ff2800','#ffcc00','#fff'], 80), 600);
}

// Exponer para uso desde PHP
window.showCompletionScreen = showCompletionScreen;

/* ══════════════════════════════════════════════════════════
   SELECCIONAR OPCIÓN
   ══════════════════════════════════════════════════════════ */
function selectOption(btn) {
  playSound('select');
  const exercise = btn.closest('.exercise');
  exercise.querySelectorAll('.option-btn').forEach(b => {
    b.classList.remove('selected', 'wrong');
  });
  btn.classList.add('selected');
  const checkBtn = exercise.querySelector('.check-btn');
  if (checkBtn) checkBtn.classList.remove('hidden');

  const feedback = exercise.querySelector('.exercise__feedback');
  if (feedback && feedback.classList.contains('exercise__feedback--wrong')) {
    feedback.classList.add('hidden');
    feedback.classList.remove('exercise__feedback--wrong');
  }
}

/* ══════════════════════════════════════════════════════════
   VERIFICAR RESPUESTA
   ══════════════════════════════════════════════════════════ */
function checkAnswer(checkBtn, exerciseId) {
  const exercise    = checkBtn.closest('.exercise');
  const selectedBtn = exercise.querySelector('.option-btn.selected');
  if (!selectedBtn) return;

  const selectedOptionId = parseInt(selectedBtn.dataset.optionId, 10);
  checkBtn.disabled = true;
  checkBtn.textContent = 'Verificando…';

  const formData = new FormData();
  formData.append('exercise_id',        exerciseId);
  formData.append('selected_option_id', selectedOptionId);

  fetch(CHECK_ANSWER_URL, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
      if (!data.success) {
        checkBtn.disabled = false;
        checkBtn.textContent = 'Verificar Respuesta';
        return;
      }

      const isCorrect       = data.correct;
      const correctOptionId = data.correctOptionId;

      if (isCorrect) {
        playSound('correct');

        exercise.querySelectorAll('.option-btn').forEach(btn => {
          btn.disabled = true;
          if (parseInt(btn.dataset.optionId, 10) === correctOptionId) btn.classList.add('correct');
        });
        checkBtn.classList.add('hidden');

        const feedback = exercise.querySelector('.exercise__feedback');
        feedback.classList.remove('hidden', 'exercise__feedback--wrong');
        feedback.classList.add('exercise__feedback--correct');
        feedback.textContent = data.feedback || '¡Correcto! 🎉';

        const explanation = exercise.querySelector('.exercise__explanation');
        if (explanation && data.explanation) {
          explanation.textContent = data.explanation;
          explanation.classList.remove('hidden');
        }

        correctCount++;
        launchConfetti(['#ff2800','#ff6b00','#ffcc00'], 35);

        // Solo mostrar celebración si es la última lección del módulo
        if (typeof IS_LAST_LESSON !== 'undefined' && IS_LAST_LESSON &&
            typeof TOTAL_EXERCISES !== 'undefined' && correctCount >= TOTAL_EXERCISES) {
          setTimeout(() => {
            if (typeof MODULE_NAME !== 'undefined') {
              showCompletionScreen(MODULE_NAME);
            }
          }, 800);
        }

      } else {
        playSound('wrong');

        exercise.querySelectorAll('.option-btn').forEach(btn => {
          if (parseInt(btn.dataset.optionId, 10) === selectedOptionId) btn.classList.add('wrong');
        });

        const feedback = exercise.querySelector('.exercise__feedback');
        feedback.classList.remove('hidden', 'exercise__feedback--correct');
        feedback.classList.add('exercise__feedback--wrong');
        feedback.textContent = data.feedback || '¡Respuesta incorrecta! Selecciona otra opción. 💡';

        checkBtn.disabled = false;
        checkBtn.textContent = 'Verificar Respuesta';
        checkBtn.classList.remove('hidden');
      }
    })
    .catch(() => {
      checkBtn.disabled = false;
      checkBtn.textContent = 'Verificar Respuesta';
      alert('Error de conexión. Intenta de nuevo.');
    });
}

/* ══════════════════════════════════════════════════════════
   CONFETTI
   ══════════════════════════════════════════════════════════ */
function launchConfetti(colors, count = 50) {
  const cols = colors || ['#ff2800','#ff6b00','#ffcc00','#fff'];
  for (let i = 0; i < count; i++) {
    const dot = document.createElement('div');
    const size = 6 + Math.random() * 8;
    const isRect = Math.random() > 0.5;
    dot.style.cssText = `
      position:fixed;
      width:${size}px; height:${isRect ? size * 0.4 : size}px;
      border-radius:${isRect ? '2px' : '50%'};
      background:${cols[i % cols.length]};
      left:${Math.random() * 100}vw; top:-10px;
      pointer-events:none; z-index:9999;
      animation:fall ${0.8 + Math.random() * 1.2}s ease-in forwards;
      transform:rotate(${Math.random()*360}deg);`;
    document.body.appendChild(dot);
    setTimeout(() => dot.remove(), 2500);
  }
}

if (!document.getElementById('confetti-style')) {
  const s = document.createElement('style');
  s.id = 'confetti-style';
  s.textContent = '@keyframes fall { to { transform: translateY(110vh) rotate(720deg); opacity:0; } }';
  document.head.appendChild(s);
}

/* ══════════════════════════════════════════════════════════
   INIT
   ══════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  initDarkMode();
  initModuleTooltips();
});
