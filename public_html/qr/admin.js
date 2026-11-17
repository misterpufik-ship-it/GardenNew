(function () {
    if (window.__glQrAdminLoaded) return;
    window.__glQrAdminLoaded = true;
    var root = document.getElementById('gl-qr-root');
    if (!root) return;
    var $ = window.jQuery;

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
        node.className = 'gl-qr-status' + (isError ? ' label label-error' : '');
    }

    function captureUi() {
        root.querySelectorAll('[data-ui][data-key]').forEach(function (node) {
            var kind = node.getAttribute('data-ui');
            var key = node.getAttribute('data-key');
            var heading = node.querySelector(':scope > .panel-toggle');
            if (!state.ui[kind]) state.ui[kind] = {};
            state.ui[kind][key] = heading ? !heading.classList.contains('collapsed') : true;
        });
    }

    function isOpen(kind, key, fallback) {
        if (state.ui[kind] && Object.prototype.hasOwnProperty.call(state.ui[kind], key)) {
            return !!state.ui[kind][key];
        }
        return !!fallback;
    }

    function groupHtml(opts) {
        var open = isOpen(opts.kind, opts.key, opts.open);
        return (
            '<div class="group-wrapper k_group gl-qr-group" data-ui="' + esc(opts.kind) + '" data-key="' + esc(opts.key) + '"' +
                (opts.attrs || '') + '>' +
                '<a class="panel-heading panel-toggle' + (open ? '' : ' collapsed') + '" href="#">' + esc(opts.title) + '</a>' +
                '<div class="panel-body"' + (open ? '' : ' style="display:none;"') + '>' +
                    opts.body +
                    '<div class="field placeholder"></div>' +
                    '<a class="btn panel-body-toggle" href="#"></a>' +
                '</div>' +
            '</div>'
        );
    }

    function fieldHtml(label, inner) {
        return (
            '<div class="field k_element k_text">' +
                '<label class="field-label">' + esc(label) + '</label><br>' +
                inner +
            '</div>'
        );
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
        var branchHtml = Object.keys(state.branches).map(function (branch, index) {
            var info = state.branches[branch];
            var codes = codesFor(branch);
            var cards = codes.map(function (code) {
                var body =
                    fieldHtml('Название', '<input type="text" class="text" size="105" data-field="title" value="' + esc(code.title) + '">') +
                    fieldHtml('Ссылка внутри QR (можно менять)', '<input type="text" class="text" size="105" data-field="target_url" value="' + esc(code.target_url) + '" placeholder="https://...">') +
                    fieldHtml('Постоянный адрес QR', '<a href="' + esc(code.stable_url) + '" target="_blank" rel="noopener">' + esc(code.stable_url) + '</a>') +
                    '<div class="field k_element">' +
                        '<button type="button" class="btn btn-primary" data-act="save">Сохранить ссылку</button> ' +
                        '<a class="btn" href="' + esc(code.png_url) + '" download="qr-' + esc(code.branch) + '-' + esc(code.slug) + '.png">Скачать PNG</a> ' +
                        '<button type="button" class="btn" data-act="copy">Копировать адрес</button>' +
                    '</div>' +
                    groupHtml({
                        kind: 'qr',
                        key: code.id,
                        title: 'QR-картинка',
                        open: false,
                        body: '<div class="field k_element gl-qr-preview"><img alt="QR ' + esc(code.title) + '" src="' + esc(code.png_url) + '"></div>'
                    });
                return groupHtml({
                    kind: 'code',
                    key: code.id,
                    title: code.title || code.slug,
                    open: false,
                    attrs: ' data-id="' + esc(code.id) + '"',
                    body: body
                });
            }).join('');
            if (!cards) {
                cards = '<div class="field k_element"><p>Пока нет QR. Создайте первый код ниже.</p></div>';
            }
            var create =
                '<form class="gl-qr-create">' +
                    fieldHtml('Название нового QR', '<input type="text" class="text" size="105" name="title" placeholder="Например, Меню">') +
                    fieldHtml('Ссылка', '<input type="text" class="text" size="105" name="target_url" placeholder="https://garden-lounge.pro/' + esc(branch) + '/menu" required>') +
                    '<div class="field k_element"><button type="submit" class="btn btn-primary">Сгенерировать постоянный QR</button></div>' +
                '</form>';
            return groupHtml({
                kind: 'branch',
                key: branch,
                title: info.label,
                open: index === 0,
                attrs: ' data-branch="' + esc(branch) + '"',
                body: create + cards
            });
        }).join('');

        root.innerHTML =
            '<div class="gl-qr-app">' +
                '<div class="field k_element k_message">' +
                    '<p>QR печатается <strong>один раз</strong>. В него вшит постоянный адрес сайта Garden Lounge. Ссылку назначения можно менять — картинка при этом не меняется и не затирается деплоем.</p>' +
                    '<p class="gl-qr-status"></p>' +
                '</div>' +
                branchHtml +
            '</div>';

        root.querySelectorAll('.gl-qr-create').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                var branch = form.closest('[data-branch]').getAttribute('data-branch');
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

        root.querySelectorAll('[data-ui="code"]').forEach(function (card) {
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
                var url = card.querySelector('a[href^="http"]').getAttribute('href');
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

    root.addEventListener('click', function (event) {
        var heading = event.target.closest('.panel-toggle, .panel-body-toggle');
        if (!heading || !root.contains(heading)) return;
        event.preventDefault();
        event.stopPropagation();
        if (heading.classList.contains('panel-body-toggle')) {
            heading = heading.parentElement.previousElementSibling;
        }
        heading.classList.toggle('collapsed');
        var body = heading.nextElementSibling;
        if ($ && body) {
            $(body).stop(true, true).animate({ height: 'toggle' }, 400);
        } else if (body) {
            body.style.display = body.style.display === 'none' ? '' : 'none';
        }
    });

    function load() {
        return api('list').then(function (data) {
            state.branches = data.branches || {};
            state.codes = data.codes || [];
            render();
        });
    }

    root.textContent = 'Загрузка…';
    load().catch(function (err) {
        root.innerHTML = '<div class="field k_element"><span class="label label-error">' + esc(err.message) + '</span></div>';
    });
})();
