(function () {
    'use strict';

    var STORAGE_KEY = 'gl_age_verified';
    var COOKIE_NAME = 'gl_age_verified';
    var DEFAULT_COOKIE_MAX_AGE = 60 * 60 * 24 * 365;
    var config = window.__glAgeGateConfig || {};

    if (window.__glAgeGateInit) {
        return;
    }
    window.__glAgeGateInit = true;

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function readCookie(name) {
        var match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : '';
    }

    function cookieMaxAge() {
        var days = parseInt(config.rememberDays, 10);
        if (isNaN(days) || days <= 0) {
            return 0;
        }
        return days * 24 * 60 * 60;
    }

    function setVerified() {
        var mode = config.storageMode || 'both';
        var maxAge = cookieMaxAge();

        if (mode === 'both' || mode === 'cookie') {
            var cookie = COOKIE_NAME + '=1; path=/; SameSite=Lax';
            if (maxAge > 0) {
                cookie += '; max-age=' + maxAge;
            }
            document.cookie = cookie;
        }

        if (mode === 'both') {
            try {
                localStorage.setItem(STORAGE_KEY, '1');
            } catch (e) {}
        }

        if (mode === 'session') {
            try {
                sessionStorage.setItem(STORAGE_KEY, '1');
            } catch (e) {}
        }
    }

    function isVerified() {
        var mode = config.storageMode || 'both';

        if (mode === 'both') {
            try {
                if (localStorage.getItem(STORAGE_KEY) === '1') {
                    return true;
                }
            } catch (e) {}
        }

        if (mode === 'both' || mode === 'cookie') {
            if (readCookie(COOKIE_NAME) === '1') {
                return true;
            }
        }

        if (mode === 'session') {
            try {
                return sessionStorage.getItem(STORAGE_KEY) === '1';
            } catch (e) {}
        }

        return false;
    }

    function lockBody(lock) {
        document.documentElement.classList.toggle('gl-age-gate-open', lock);
        document.body.classList.toggle('gl-age-gate-open', lock);
    }

    function cfg(key, fallback) {
        return (config[key] !== undefined && config[key] !== null && config[key] !== '')
            ? config[key]
            : fallback;
    }

    function buildGate() {
        var logo = cfg('logo', '/admiralteyskaya/couch/uploads/image/logo3.webp');
        var badge = cfg('badge', '18+');
        var title = cfg('title', 'Вам уже исполнилось 18 лет?');
        var welcome = cfg('welcome', 'Добро пожаловать в Garden Lounge.');
        var description = cfg('description', 'Перед входом подтвердите возраст, чтобы продолжить знакомство с нашим садом. Сайт содержит информацию о заведении, где представлены кальяны и табачная продукция.');
        var btnYes = cfg('btnYes', 'Да, войти');
        var btnNo = cfg('btnNo', 'Нет, покинуть сайт');
        var deniedText = cfg('deniedText', 'Дальнейшее отображение материалов сайта невозможно');

        var overlay = document.createElement('div');
        overlay.id = 'gl-age-gate';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-labelledby', 'gl-age-gate-title');

        overlay.innerHTML =
            '<div class="gl-age-gate__panel">' +
                '<img class="gl-age-gate__logo" src="' + escapeHtml(logo) + '" alt="Garden Lounge" width="260" height="80" decoding="async">' +
                '<div class="gl-age-gate__badge">' + escapeHtml(badge) + '</div>' +
                '<h1 class="gl-age-gate__title" id="gl-age-gate-title">' + escapeHtml(title) + '</h1>' +
                '<p class="gl-age-gate__welcome">' + escapeHtml(welcome) + '</p>' +
                '<p class="gl-age-gate__text">' + escapeHtml(description) + '</p>' +
                '<div class="gl-age-gate__actions" id="gl-age-gate-actions">' +
                    '<button type="button" class="gl-age-gate__btn gl-age-gate__btn--yes" id="gl-age-gate-yes">' + escapeHtml(btnYes) + '</button>' +
                    '<button type="button" class="gl-age-gate__btn" id="gl-age-gate-no">' + escapeHtml(btnNo) + '</button>' +
                '</div>' +
                '<div class="gl-age-gate__denied" id="gl-age-gate-denied" aria-live="polite">' + escapeHtml(deniedText) + '</div>' +
            '</div>';

        document.body.appendChild(overlay);
        lockBody(true);

        var yesBtn = overlay.querySelector('#gl-age-gate-yes');
        var noBtn = overlay.querySelector('#gl-age-gate-no');
        var actions = overlay.querySelector('#gl-age-gate-actions');
        var denied = overlay.querySelector('#gl-age-gate-denied');

        yesBtn.addEventListener('click', function () {
            setVerified();
            overlay.classList.add('is-hidden');
            lockBody(false);
            overlay.remove();
        });

        noBtn.addEventListener('click', function () {
            actions.style.display = 'none';
            denied.classList.add('is-visible');
        });

        yesBtn.focus();
    }

    function init() {
        if (isVerified()) {
            return;
        }

        if (document.body) {
            buildGate();
        } else {
            document.addEventListener('DOMContentLoaded', buildGate, { once: true });
        }
    }

    init();
})();
