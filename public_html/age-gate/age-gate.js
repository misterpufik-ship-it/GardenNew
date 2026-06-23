(function () {
    'use strict';

    var STORAGE_KEY = 'gl_age_verified';
    var COOKIE_NAME = 'gl_age_verified';
    var COOKIE_MAX_AGE = 60 * 60 * 24 * 365;

    if (window.__glAgeGateInit) {
        return;
    }
    window.__glAgeGateInit = true;

    function readCookie(name) {
        var match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : '';
    }

    function setVerified() {
        try {
            localStorage.setItem(STORAGE_KEY, '1');
        } catch (e) {}

        document.cookie = COOKIE_NAME + '=1; path=/; max-age=' + COOKIE_MAX_AGE + '; SameSite=Lax';
    }

    function isVerified() {
        try {
            if (localStorage.getItem(STORAGE_KEY) === '1') {
                return true;
            }
        } catch (e) {}

        return readCookie(COOKIE_NAME) === '1';
    }

    function lockBody(lock) {
        document.documentElement.classList.toggle('gl-age-gate-open', lock);
        document.body.classList.toggle('gl-age-gate-open', lock);
    }

    function isUdelnayaBranch() {
        return /\/udelnaya(?:\/|$)/i.test(window.location.pathname);
    }

    function getWelcomeText() {
        if (isUdelnayaBranch()) {
            return 'Добро пожаловать в Garden Lounge на Удельной.';
        }

        return 'Добро пожаловать в Garden Lounge.';
    }

    function buildGate() {
        var overlay = document.createElement('div');
        overlay.id = 'gl-age-gate';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-labelledby', 'gl-age-gate-title');

        overlay.innerHTML =
            '<div class="gl-age-gate__panel">' +
                '<img class="gl-age-gate__logo" src="/admiralteyskaya/couch/uploads/image/logo3.webp" alt="Garden Lounge" width="260" height="80" decoding="async">' +
                '<div class="gl-age-gate__badge">18+</div>' +
                '<h1 class="gl-age-gate__title" id="gl-age-gate-title">Вам уже исполнилось 18 лет?</h1>' +
                '<p class="gl-age-gate__welcome">' + getWelcomeText() + '</p>' +
                '<p class="gl-age-gate__text">Перед входом подтвердите возраст, чтобы продолжить знакомство с нашим садом. Сайт содержит информацию о заведении, где представлены кальяны и табачная продукция.</p>' +
                '<div class="gl-age-gate__actions" id="gl-age-gate-actions">' +
                    '<button type="button" class="gl-age-gate__btn gl-age-gate__btn--yes" id="gl-age-gate-yes">Да, войти</button>' +
                    '<button type="button" class="gl-age-gate__btn" id="gl-age-gate-no">Нет, покинуть сайт</button>' +
                '</div>' +
                '<div class="gl-age-gate__denied" id="gl-age-gate-denied" aria-live="polite">Дальнейшее отображение материалов сайта невозможно</div>' +
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
