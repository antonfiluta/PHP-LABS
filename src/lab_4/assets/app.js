const USER = window.__USER__ || 'guest';
const STORAGE_KEY = 'edu_progress_' + USER;

let currentLang = window.__LANG__ || 'ru';
let currentTheme = window.__THEME__ || 'dark';

const TRANSLATIONS = {
  ru: {
    greeting: 'Привет',
    group: 'Группа',
    completed: 'Завершено',
    settings: 'Настройки',
    theme: 'Тема',
    dark: 'Тёмная',
    light: 'Светлая',
    language: 'Язык',
    russian: 'Русский',
    english: 'English',
    sync: 'Синхронизировать прогресс',
    completed_status: 'Завершён',
    not_started: 'Не начат',
    toast_sync_ok: '✓ Прогресс синхронизирован',
    toast_theme_dark: 'Тёмная тема',
    toast_theme_light: 'Светлая тема',
    toast_lang_ru: 'Русский язык',
    toast_lang_en: 'English language',
    toast_error: '✗ Ошибка синхронизации',
    reset: 'Сбросить прогресс',
    toast_reset_ok: '✓ Прогресс сброшен',
    toast_reset_cancel: 'Сброс отменён',
  },
  en: {
    greeting: 'Hello',
    group: 'Group',
    completed: 'Completed',
    settings: 'Settings',
    theme: 'Theme',
    dark: 'Dark',
    light: 'Light',
    language: 'Language',
    russian: 'Russian',
    english: 'English',
    sync: 'Sync progress',
    completed_status: 'Completed',
    not_started: 'Not started',
    toast_sync_ok: '✓ Progress synced',
    toast_theme_dark: 'Dark theme',
    toast_theme_light: 'Light theme',
    toast_lang_ru: 'Russian language',
    toast_lang_en: 'English language',
    toast_error: '✗ Sync error',
    reset: 'Reset progress',
    toast_reset_ok: '✓ Progress reset',
    toast_reset_cancel: 'Reset cancelled',
  },
};

function applyTheme() {
  document.documentElement.dataset.theme = currentTheme;
  if (currentTheme === 'light') {
    document.body.style.filter = 'invert(1) hue-rotate(180deg)';
    document.body.style.background = '#fff';
  } else {
    document.body.style.filter = '';
    document.body.style.background = '';
  }
}

function applyTranslations() {
  const t = TRANSLATIONS[currentLang] || TRANSLATIONS.ru;
  document.querySelectorAll('[data-i18n]').forEach((el) => {
    const key = el.dataset.i18n;
    if (t[key] !== undefined) el.textContent = t[key];
  });
  document.documentElement.dataset.lang = currentLang;
  document.documentElement.lang = currentLang;
}

async function resetProgress() {
  const res = await fetch('reset.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
  });
  const data = await res.json();
  if (data.status === 'ok') {
    localStorage.removeItem(STORAGE_KEY);
    updateUi();
  }
  return data;
}

let toastTimer;
function showToast(message, kind = 'info') {
  const el = document.getElementById('toast');
  if (!el) return;
  el.textContent = message;
  el.classList.remove(
    'hidden',
    'border-red-400/30',
    'bg-red-500/10',
    'text-red-200',
    'border-sky-400/30',
    'bg-sky-500/10',
    'text-sky-200',
  );
  if (kind === 'error') {
    el.classList.add('border-red-400/30', 'bg-red-500/10', 'text-red-200');
  } else {
    el.classList.add('border-sky-400/30', 'bg-sky-500/10', 'text-sky-200');
  }
  el.style.opacity = '1';
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    el.style.opacity = '0';
    setTimeout(() => el.classList.add('hidden'), 300);
  }, 2200);
}

function loadProgress() {
  const raw = localStorage.getItem(STORAGE_KEY);
  const local = raw ? JSON.parse(raw) : {};
  const server = window.__SERVER_PROGRESS__ || {};

  const merged = { ...local };
  for (const [id, data] of Object.entries(server)) {
    const localDone = merged[id]?.done ?? false;
    const serverDone = data.done ?? false;
    merged[id] = { done: localDone || serverDone, ts: Date.now() };
  }
  localStorage.setItem(STORAGE_KEY, JSON.stringify(merged));
  return merged;
}

function saveProgress(moduleId, done) {
  const progress = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
  progress[moduleId] = { done, ts: Date.now() };
  localStorage.setItem(STORAGE_KEY, JSON.stringify(progress));
}

function updateProgressBar() {
  const progress = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
  const total = document.querySelectorAll('.module-toggle').length;
  const done = Object.values(progress).filter((p) => p.done).length;
  const percent = total > 0 ? Math.round((done / total) * 100) : 0;

  const bar = document.getElementById('progressBar');
  if (bar) bar.style.width = percent + '%';

  const percentEl = document.getElementById('progressPercent');
  if (percentEl) percentEl.textContent = percent;
  const countEl = document.getElementById('progressCount');
  if (countEl) countEl.textContent = done;
  const totalEl = document.getElementById('progressTotal');
  if (totalEl) totalEl.textContent = total;
}

function updateUi() {
  const progress = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
  const t = TRANSLATIONS[currentLang] || TRANSLATIONS.ru;

  document.querySelectorAll('.module-toggle').forEach((toggle) => {
    const id = toggle.dataset.module;
    const done = progress[id]?.done ?? false;
    toggle.checked = done;

    const status = toggle
      .closest('[data-module]')
      ?.querySelector('.module-status');
    if (status) {
      status.textContent = done ? t.completed_status : t.not_started;
      status.classList.toggle('text-sky-300', done);
      status.classList.toggle('text-stone-500', !done);
    }
  });

  updateProgressBar();
}

async function pushProgress() {
  const progress = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
  const res = await fetch('sync.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ progress }),
  });
  const data = await res.json();
  if (data.progress) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data.progress));
    updateUi();
  }
  return data;
}

async function saveSettings() {
  await fetch('settings.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ theme: currentTheme, lang: currentLang }),
  });
}

document.addEventListener('DOMContentLoaded', () => {
  applyTheme();
  applyTranslations();
  loadProgress();
  updateUi();

  document.querySelectorAll('.module-toggle').forEach((toggle) => {
    toggle.addEventListener('change', async () => {
      const id = toggle.dataset.module;
      saveProgress(id, toggle.checked);
      updateUi();
      try {
        await pushProgress();
      } catch (e) {
        /* тихо игнорируем */
      }
    });
  });

  // Синхронизация вручную — с видимым откликом
  document.getElementById('syncBtn')?.addEventListener('click', async () => {
    try {
      await pushProgress();
      showToast(TRANSLATIONS[currentLang].toast_sync_ok);
    } catch (e) {
      showToast(TRANSLATIONS[currentLang].toast_error, 'error');
    }
  });

  document.getElementById('resetBtn')?.addEventListener('click', async () => {
    const t = TRANSLATIONS[currentLang];
    if (!confirm(t.reset + '?')) {
      showToast(t.toast_reset_cancel);
      return;
    }
    try {
      await resetProgress();
      showToast(t.toast_reset_ok);
    } catch (e) {
      showToast(t.toast_error, 'error');
    }
  });

  document
    .getElementById('themeSelect')
    ?.addEventListener('change', async (e) => {
      currentTheme = e.target.value;
      applyTheme();
      await saveSettings();
      showToast(
        currentTheme === 'light'
          ? TRANSLATIONS[currentLang].toast_theme_light
          : TRANSLATIONS[currentLang].toast_theme_dark,
      );
    });

  document
    .getElementById('langSelect')
    ?.addEventListener('change', async (e) => {
      currentLang = e.target.value;
      applyTranslations();
      updateUi();
      await saveSettings();
      showToast(
        currentLang === 'en'
          ? TRANSLATIONS.en.toast_lang_en
          : TRANSLATIONS.ru.toast_lang_ru,
      );
    });
});
