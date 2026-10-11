<?php
declare(strict_types=1);
/** @var string $storageType */
?>
<!DOCTYPE html>
<html lang="ru" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP LABS | Учебные дисциплины</title>
    <link href="../../output.css" rel="stylesheet">
</head>

<body class="min-h-screen antialiased">
    <div class="fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-stone-950 via-stone-950 to-stone-900">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div
            class="absolute top-1/2 left-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl bg-sky-400/10">
        </div>
    </div>

    <div id="toast"
        class="fixed top-20 right-6 z-50 hidden rounded-lg border border-sky-400/30 bg-sky-500/10 px-4 py-3 text-sm text-sky-200 backdrop-blur transition-opacity duration-300">
    </div>

    <header class="fixed top-4 left-4 right-4 z-20 flex flex-wrap items-center gap-3">
        <a href="../../index.php"
            class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium backdrop-blur-md hover:bg-white/10!">
            PHP LABS
        </a>
        <a href="../lab-base.php"
            class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium backdrop-blur-md hover:bg-white/10!">
            ← Назад
        </a>
    </header>

    <main class="relative min-h-screen px-6 py-28">
        <div class="mx-auto w-full max-w-6xl">

            <div class="mb-10 text-center">
                <p class="php-variant">Вариант 13</p>
                <h1 class="php-h1">Список учебных дисциплин</h1>
                <p class="text-lg text-stone-400">MVC · JSON/XML · Ajax · Экспорт</p>
            </div>

            <!-- Панель управления -->
            <div class="block-bg p-4 mb-6 flex flex-wrap gap-4 items-center justify-between">
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 text-sm text-stone-300">
                        <span data-i18n="storage">Хранилище</span>:
                        <select id="storageSelect"
                            class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-stone-100">
                            <option value="json" <?= $storageType === 'json' ? 'selected' : '' ?>>JSON</option>
                            <option value="xml" <?= $storageType === 'xml' ? 'selected' : '' ?>>XML</option>
                        </select>
                    </label>

                    <label class="flex items-center gap-2 text-sm text-stone-300">
                        <span data-i18n="theme">Тема</span>:
                        <select id="themeSelect"
                            class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-stone-100">
                            <option value="dark">Тёмная</option>
                            <option value="light">Светлая</option>
                        </select>
                    </label>

                    <label class="flex items-center gap-2 text-sm text-stone-300">
                        <span data-i18n="language">Язык</span>:
                        <select id="langSelect"
                            class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-stone-100">
                            <option value="ru">Русский</option>
                            <option value="en">English</option>
                        </select>
                    </label>
                </div>


                <div class="flex gap-4">
                    <a href="export.php"
                        class="rounded-lg border border-emerald-400/30 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-300 hover:bg-emerald-500/20"
                        data-i18n="export_csv">
                        Экспорт в CSV
                    </a>

                    <a href="export_pdf.php"
                        class="rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-300 hover:bg-red-500/20"
                        data-i18n="export_pdf">
                        Экспорт в PDF
                    </a>
                </div>

            </div>

            <!-- Фильтры и статистика -->
            <div class="block-bg p-4 mb-6 flex flex-wrap gap-4 items-end">
                <label class="flex flex-col gap-1 text-sm text-stone-300">
                    <span data-i18n="semester">Семестр</span>
                    <select id="filterSemester"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-stone-100">
                        <option value="">Все</option>
                        <?php for ($s = 1; $s <= 8; $s++): ?>
                            <option value="<?= $s ?>"><?= $s ?></option>
                        <?php endfor; ?>
                    </select>
                </label>

                <label class="flex flex-col gap-1 text-sm text-stone-300 flex-1 min-w-52">
                    <span data-i18n="search">Поиск по преподавателю</span>
                    <input id="filterTeacher" type="text"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-stone-100 focus:border-sky-400 focus:outline-none"
                        placeholder="Иванов И.И.">
                </label>

                <div id="statsBox" class="text-sm text-stone-400"></div>
            </div>

            <!-- Таблица -->
            <div class="block-bg overflow-x-auto mb-6">
                <table class="w-full text-sm">
                    <thead class="bg-white/5 text-stone-300">
                        <tr>
                            <th class="px-4 py-3 text-left cursor-pointer" data-sort="name">Название ↕</th>
                            <th class="px-4 py-3 text-left cursor-pointer" data-sort="teacher">Преподаватель ↕</th>
                            <th class="px-4 py-3 text-left cursor-pointer" data-sort="semester">Семестр ↕</th>
                            <th class="px-4 py-3 text-left cursor-pointer" data-sort="hours">Часы ↕</th>
                            <th class="px-4 py-3 text-left cursor-pointer" data-sort="faculty">Факультет ↕</th>
                            <th class="px-4 py-3 text-center">Действия</th>
                        </tr>
                    </thead>
                    <tbody id="disciplineTable"></tbody>
                </table>
            </div>

            <!-- Форма добавления -->
            <div class="block-bg p-6">
                <p class="php-h2">Добавить дисциплину</p>
                <form id="addForm" class="grid gap-3 md:grid-cols-5">
                    <input name="name" required placeholder="Название"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 focus:border-sky-400 focus:outline-none">
                    <input name="teacher" required placeholder="Преподаватель"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 focus:border-sky-400 focus:outline-none">
                    <input name="semester" required type="number" min="1" max="12" placeholder="Семестр"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 focus:border-sky-400 focus:outline-none">
                    <input name="hours" required type="number" min="1" max="500" placeholder="Часы"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 focus:border-sky-400 focus:outline-none">
                    <input name="faculty" required placeholder="Факультет"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 focus:border-sky-400 focus:outline-none">
                    <button type="submit"
                        class="md:col-span-5 rounded-lg bg-sky-500 px-4 py-2 font-semibold text-white hover:bg-sky-400"
                        data-i18n="add">
                        Добавить
                    </button>
                </form>
            </div>

            <footer class="mt-10 text-center text-xs text-stone-500">
                © <?= date('Y') ?> · PHP Labs
            </footer>
        </div>
    </main>

    <script>
        const STORAGE_KEY_THEME = 'lab56_theme';
        const STORAGE_KEY_LANG = 'lab56_lang';

        // Текущее состояние
        let state = {
            items: [],
            semester: '',
            teacher: '',
            sortBy: 'name',
            sortDir: 'asc',
            storage: <?= json_encode($storageType) ?>,
        };

        const t = {
            ru: {
                empty: 'Нет данных',
                delete: 'Удалить',
                confirm: 'Удалить запись?',
                saved: 'Сохранено',
                error: 'Ошибка',
                added: 'Дисциплина добавлена',
                deleted: 'Запись удалена',
                switched: 'Хранилище переключено на',
                total: 'Всего',
                hours: 'часов',
                dark: 'Тёмная тема',
                light: 'Светлая тема',
            },
            en: {
                empty: 'No data',
                delete: 'Delete',
                confirm: 'Delete this record?',
                saved: 'Saved',
                error: 'Error',
                added: 'Discipline added',
                deleted: 'Record deleted',
                switched: 'Storage switched to',
                total: 'Total',
                hours: 'hours',
                dark: 'Dark theme',
                light: 'Light theme',
            },
        };

        function lang() { return localStorage.getItem(STORAGE_KEY_LANG) || 'ru'; }
        function tr(key) { return t[lang()][key] || key; }

        // ---------------- Toast ----------------
        let toastTimer;
        function showToast(message, kind = 'info') {
            const el = document.getElementById('toast');
            el.textContent = message;
            el.classList.remove('hidden', 'border-red-400/30', 'bg-red-500/10', 'text-red-200');
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

        // ---------------- Тема ----------------
        function applyTheme() {
            const theme = localStorage.getItem(STORAGE_KEY_THEME) || 'dark';
            document.querySelector('#themeSelect').value = theme;
            if (theme === 'light') {
                document.body.style.filter = 'invert(1) hue-rotate(180deg)';
                document.body.style.background = '#fff';
            } else {
                document.body.style.filter = '';
                document.body.style.background = '';
            }
        }

        // ---------------- Язык ----------------
        function applyLang() {
            document.querySelector('#langSelect').value = lang();
        }

        // ---------------- Ajax ----------------
        async function api(action, payload = {}) {
            const res = await fetch(`api.php?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            return await res.json();
        }

        async function loadItems() {
            const data = await api('get', {
                semester: state.semester,
                teacher: state.teacher,
                sortBy: state.sortBy,
                sortDir: state.sortDir,
            });
            if (!data.success) return;

            state.items = data.items;
            renderItems();
            renderStats(data.stats);
        }

        function renderItems() {
            const table = document.getElementById('disciplineTable');
            if (!state.items.length) {
                table.innerHTML = `<tr><td colspan="6" class="px-4 py-8 text-center text-stone-500">${tr('empty')}</td></tr>`;
                return;
            }

            table.innerHTML = state.items.map(item => `
                <tr class="border-t border-white/5 hover:bg-white/5">
                    <td class="px-4 py-2">
                        <input value="${escapeAttr(item.name)}" data-id="${item.id}" data-field="name"
                            class="edit-input w-full rounded border border-transparent bg-transparent px-2 py-1 text-stone-100 hover:border-white/10 focus:border-sky-400 focus:outline-none">
                    </td>
                    <td class="px-4 py-2">
                        <input value="${escapeAttr(item.teacher)}" data-id="${item.id}" data-field="teacher"
                            class="edit-input w-full rounded border border-transparent bg-transparent px-2 py-1 text-stone-100 hover:border-white/10 focus:border-sky-400 focus:outline-none">
                    </td>
                    <td class="px-4 py-2">
                        <input type="number" value="${item.semester}" data-id="${item.id}" data-field="semester"
                            class="edit-input w-20 rounded border border-transparent bg-transparent px-2 py-1 text-stone-100 hover:border-white/10 focus:border-sky-400 focus:outline-none">
                    </td>
                    <td class="px-4 py-2">
                        <input type="number" value="${item.hours}" data-id="${item.id}" data-field="hours"
                            class="edit-input w-20 rounded border border-transparent bg-transparent px-2 py-1 text-stone-100 hover:border-white/10 focus:border-sky-400 focus:outline-none">
                    </td>
                    <td class="px-4 py-2">
                        <input value="${escapeAttr(item.faculty)}" data-id="${item.id}" data-field="faculty"
                            class="edit-input w-full rounded border border-transparent bg-transparent px-2 py-1 text-stone-100 hover:border-white/10 focus:border-sky-400 focus:outline-none">
                    </td>
                    <td class="px-4 py-2 text-center">
                        <button data-delete="${item.id}"
                            class="rounded border border-red-400/30 bg-red-500/10 px-3 py-1 text-xs text-red-300 hover:bg-red-500/20">
                            ${tr('delete')}
                        </button>
                    </td>
                </tr>
            `).join('');

            // Inline-редактирование
            table.querySelectorAll('.edit-input').forEach(input => {
                input.addEventListener('change', async () => {
                    const id = parseInt(input.dataset.id);
                    const field = input.dataset.field;
                    const value = input.value;
                    const res = await api('edit', { id, field, value });
                    if (res.success) {
                        showToast(tr('saved'));
                        loadItems();
                    } else {
                        showToast(tr('error'), 'error');
                    }
                });
            });

            // Удаление
            table.querySelectorAll('[data-delete]').forEach(btn => {
                btn.addEventListener('click', async () => {
                    if (!confirm(tr('confirm'))) return;
                    const id = parseInt(btn.dataset.delete);
                    const res = await api('delete', { id });
                    if (res.success) {
                        showToast(tr('deleted'));
                        loadItems();
                    }
                });
            });
        }

        function renderStats(stats) {
            const box = document.getElementById('statsBox');
            if (!stats) { box.textContent = ''; return; }

            const byFac = Object.entries(stats.byFaculty)
                .map(([k, v]) => `${k}: ${v}`).join(' · ');

            box.innerHTML = `
                <div class="text-stone-300">${tr('total')}: <span class="text-sky-300">${stats.total}</span> · ${stats.hours} ${tr('hours')}</div>
                <div class="text-xs text-stone-500 mt-1">${byFac}</div>
            `;
        }

        function escapeAttr(str) {
            return String(str)
                .replaceAll('&', '&amp;')
                .replaceAll('"', '&quot;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;');
        }

        // ---------------- Инициализация ----------------
        document.addEventListener('DOMContentLoaded', () => {
            applyTheme();
            applyLang();

            // Тема
            document.getElementById('themeSelect').addEventListener('change', e => {
                localStorage.setItem(STORAGE_KEY_THEME, e.target.value);
                applyTheme();
                showToast(e.target.value === 'light' ? tr('light') : tr('dark'));
            });

            // Язык
            document.getElementById('langSelect').addEventListener('change', e => {
                localStorage.setItem(STORAGE_KEY_LANG, e.target.value);
                applyLang();
                loadItems();
            });

            // Сортировка по клику на заголовок
            document.querySelectorAll('[data-sort]').forEach(th => {
                th.addEventListener('click', () => {
                    const field = th.dataset.sort;
                    if (state.sortBy === field) {
                        state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
                    } else {
                        state.sortBy = field;
                        state.sortDir = 'asc';
                    }
                    loadItems();
                });
            });

            // Фильтры
            document.getElementById('filterSemester').addEventListener('change', e => {
                state.semester = e.target.value;
                loadItems();
            });
            document.getElementById('filterTeacher').addEventListener('input', e => {
                state.teacher = e.target.value;
                loadItems();
            });

            // Добавление
            document.getElementById('addForm').addEventListener('submit', async e => {
                e.preventDefault();
                const fd = new FormData(e.target);
                const res = await api('add', {
                    name: fd.get('name'),
                    teacher: fd.get('teacher'),
                    semester: fd.get('semester'),
                    hours: fd.get('hours'),
                    faculty: fd.get('faculty'),
                });
                if (res.success) {
                    e.target.reset();
                    showToast(tr('added'));
                    loadItems();
                } else {
                    showToast(res.error || tr('error'), 'error');
                }
            });

            // Переключение хранилища
            document.getElementById('storageSelect').addEventListener('change', async e => {
                const type = e.target.value;
                const res = await api('switch', { type });
                if (res.success) {
                    state.storage = type;
                    showToast(`${tr('switched')} ${type.toUpperCase()}`);
                    loadItems();
                }
            });

            loadItems();
        });
    </script>
</body>

</html>