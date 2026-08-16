(function () {
    if (window.__glQrAdminLoaded) return;
    window.__glQrAdminLoaded = true;
    var root = document.getElementById('gl-qr-root');
    if (!root) return;

    var state = { branches: {}, codes: [], ui: { branch: {}, code: {}, qr: {} } };

    function esc(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function setStatus(text, isError) {
        var node = root.querySelector('.gl-qr-status');
        if (!node) return;
        node.textContent = text || '';
        node.classList.toggle('is-error', !!isError);
    }

    function captureUi() {
        root.querySelectorAll('details[data-ui][data-key]').forEach(function (node) {
            var kind = node.getAttribute('data-ui');
            var key = node.getAttribute('data-key');
            if (!state.ui[kind]) state.ui[kind] = {};
            state.ui[kind][key] = node.open;
        });
    }

    function openAttr(kind, key, fallback) {
        if (state.ui[kind] && Object.prototype.hasOwnProperty.call(state.ui[kind], key)) {
            return state.ui[kind][key] ? ' open' : '';
        }
        return fallback ? ' open' : '';
    }

    function api(action, payload) {
        var opts = { credentials: 'include', headers: { Accept: 'application/json' } };
        if (payload) {
            opts.method = 'POST';
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(payload);
        }
        return fetch('/admiralteyskaya/qr-api.php?action=' + encodeURIComponent(action), opts).then(function (res) {
            return res.text().then(function (text) {
                var data = null;
                try {
                    data = text ? JSON.parse(text) : null;
                } catch (err) {
                    throw new Error('Сервер вернул не JSON');
                }
                if (!data || !res.ok || !data.ok) {
                    throw new Error((data && data.error) || 'Ошибка запроса');
                }
                return data;
            });
        });
    }

    function codesFor(branch) {
        return state.codes.filter(function (code) {
            return code.branch === branch;
        });
    }

    function render() {
        var branchKeys = Object.keys(state.branches);
        var branchHtml = branchKeys.map(function (branch, index) {
            var info = state.branches[branch];
            var codes = codesFor(branch);
            var cards = codes.map(function (code) {
                return (
                    '<details class="gl-qr-card" data-ui="code" data-key="' + esc(code.id) + '" data-id="' + esc(code.id) + '"' + openAttr('code', code.id, false) + '>' +
                        '<summary>' + esc(code.title || code.slug) + '</summary>' +
                        '<div class="gl-qr-card-body">' +
                            '<label>Название<input data-field="title" value="' + esc(code.title) + '"></label>' +
                            '<label>Ссылка внутри QR (можно менять)<input data-field="target_url" value="' + esc(code.target_url) + '" placeholder="https://..."></label>' +
                            '<div class="gl-qr-stable">Постоянный адрес QR: <a href="' + esc(code.stable_url) + '" target="_blank" rel="noopener">' + esc(code.stable_url) + '</a></div>' +
                            '<div class="gl-qr-actions">' +
                                '<button type="button" class="gl-qr-btn is-primary" data-act="save">Сохранить ссылку</button>' +
                                '<a class="gl-qr-btn" href="' + esc(code.png_url) + '" download="qr-' + esc(code.branch) + '-' + esc(code.slug) + '.png">Скачать PNG</a>' +
                                '<button type="button" class="gl-qr-btn is-ghost" data-act="copy">Копировать адрес</button>' +
                            '</div>' +
                            '<details class="gl-qr-pic" data-ui="qr" data-key="' + esc(code.id) + '"' + openAttr('qr', code.id, false) + '>' +
                                '<summary>Показать QR</summary>' +
                                '<div class="gl-qr-preview"><img alt="QR ' + esc(code.title) + '" src="' + esc(code.png_url) + '"></div>' +
                            '</details>' +
                        '</div>' +
                    '</details>'
                );
            }).join('');
            if (!cards) {
                cards = '<p class="gl-qr-empty">Пока нет QR. Создайте первый код ниже.</p>';
            }
            return (
                '<details class="gl-qr-branch" data-ui="branch" data-key="' + esc(branch) + '" data-branch="' + esc(branch) + '"' + openAttr('branch', branch, index === 0) + '>' +
                    '<summary>' + esc(info.label) + '</summary>' +
                    '<div class="gl-qr-branch-body">' +
                        '<form class="gl-qr-create">' +
                            '<input name="title" placeholder="Название, например Меню">' +
                            '<input name="target_url" placeholder="Ссылка, например https://garden-lounge.pro/' + esc(branch) + '/menu" required>' +
                            '<button type="submit" class="gl-qr-btn is-primary">Сгенерировать постоянный QR</button>' +
                        '</form>' +
                        '<div class="gl-qr-code-list">' + cards + '</div>' +
                    '</div>' +
                '</details>'
            );
        }).join('');

        root.innerHTML =
            '<div class="gl-qr-app">' +
                '<p class="gl-qr-intro">QR печатается <strong>один раз</strong>: в него вшит постоянный адрес сайта Garden Lounge. Назначение меняется в поле ссылки — картинка при этом не меняется и не затирается деплоем.</p>' +
                '<p class="gl-qr-status"></p>' +
                '<div class="gl-qr-branches">' + branchHtml + '</div>' +
            '</div>';

        root.querySelectorAll('.gl-qr-create').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                var branch = form.closest('.gl-qr-branch').getAttribute('data-branch');
                var title = form.querySelector('[name="title"]').value;
                var target = form.querySelector('[name="target_url"]').value;
                captureUi();
                if (!state.ui.branch) state.ui.branch = {};
                state.ui.branch[branch] = true;
                setStatus('Создаём QR…');
                api('create', { branch: branch, title: title, target_url: target })
                    .then(load)
                    .then(function () { setStatus('QR создан и сохранён. Картинка зафиксирована.'); })
                    .catch(function (err) { setStatus(err.message, true); });
            });
        });

        root.querySelectorAll('.gl-qr-card').forEach(function (card) {
            var id = card.getAttribute('data-id');
            card.querySelector('[data-act="save"]').addEventListener('click', function () {
                var title = card.querySelector('[data-field="title"]').value;
                var target = card.querySelector('[data-field="target_url"]').value;
                captureUi();
                setStatus('Сохраняем ссылку…');
                api('update', { id: id, title: title, target_url: target })
                    .then(load)
                    .then(function () { setStatus('Ссылка обновлена. Картинка QR не менялась.'); })
                    .catch(function (err) { setStatus(err.message, true); });
            });
            card.querySelector('[data-act="copy"]').addEventListener('click', function () {
                var url = card.querySelector('.gl-qr-stable a').getAttribute('href');
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function () {
                        setStatus('Постоянный адрес скопирован.');
                    }).catch(function () {
                        window.prompt('Скопируйте адрес QR', url);
                    });
                } else {
                    window.prompt('Скопируйте адрес QR', url);
                }
            });
        });
    }

    function load() {
        return api('list').then(function (data) {
            state.branches = data.branches || {};
            state.codes = data.codes || [];
            render();
        });
    }

    root.textContent = 'Загрузка…';
    load().catch(function (err) {
        root.innerHTML = '<p class="gl-qr-status is-error">' + esc(err.message) + '</p>';
    });
})();
