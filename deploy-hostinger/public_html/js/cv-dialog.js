/**
 * Diálogos personalizados Compraventa
 * CvDialog.alert() · CvDialog.confirm()
 */
(function (window) {
    'use strict';

    var backdrop = null;

    function ensureBackdrop() {
        if (backdrop) return backdrop;

        backdrop = document.createElement('div');
        backdrop.className = 'cv-dialog-backdrop';
        backdrop.innerHTML =
            '<div class="cv-dialog" role="dialog" aria-modal="true" aria-labelledby="cv-dialog-title">' +
                '<h2 class="cv-dialog-title" id="cv-dialog-title"></h2>' +
                '<p class="cv-dialog-message" id="cv-dialog-message"></p>' +
                '<div class="cv-dialog-actions" id="cv-dialog-actions"></div>' +
            '</div>';
        document.body.appendChild(backdrop);
        return backdrop;
    }

    function openDialog() {
        var el = ensureBackdrop();
        requestAnimationFrame(function () {
            el.classList.add('cv-dialog-open');
        });
    }

    function closeDialog() {
        if (!backdrop) return;
        backdrop.classList.remove('cv-dialog-open');
    }

    function makeButton(label, className, onClick) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'cv-dialog-btn ' + className;
        btn.textContent = label;
        btn.addEventListener('click', onClick);
        return btn;
    }

    function alert(options) {
        options = options || {};
        var el = ensureBackdrop();
        var title = el.querySelector('#cv-dialog-title');
        var message = el.querySelector('#cv-dialog-message');
        var actions = el.querySelector('#cv-dialog-actions');

        title.textContent = options.title || 'Atención';
        message.textContent = options.message || '';
        actions.innerHTML = '';

        var btnClass = options.type === 'success' ? 'cv-dialog-btn-success' : 'cv-dialog-btn-primary';
        var btnLabel = options.confirmText || 'Entendido';

        actions.appendChild(makeButton(btnLabel, btnClass, function () {
            closeDialog();
            if (typeof options.onClose === 'function') {
                options.onClose();
            }
        }));

        openDialog();
    }

    function confirm(options) {
        options = options || {};

        return new Promise(function (resolve) {
            var el = ensureBackdrop();
            var title = el.querySelector('#cv-dialog-title');
            var message = el.querySelector('#cv-dialog-message');
            var actions = el.querySelector('#cv-dialog-actions');

            title.textContent = options.title || '¿Está seguro?';
            message.textContent = options.message || '';
            actions.innerHTML = '';

            actions.appendChild(makeButton(
                options.confirmText || 'Sí, continuar',
                'cv-dialog-btn-primary',
                function () {
                    closeDialog();
                    resolve(true);
                    if (typeof options.onConfirm === 'function') {
                        options.onConfirm();
                    }
                }
            ));

            actions.appendChild(makeButton(
                options.cancelText || 'Cancelar',
                'cv-dialog-btn-cancel',
                function () {
                    closeDialog();
                    resolve(false);
                }
            ));

            openDialog();
        });
    }

    function readConfirmOptions(el) {
        return {
            title: el.getAttribute('data-cv-title') || '¿Está seguro?',
            message: el.getAttribute('data-cv-message') || '',
            confirmText: el.getAttribute('data-cv-confirm') || 'Sí, continuar',
            cancelText: el.getAttribute('data-cv-cancel') || 'Cancelar'
        };
    }

    function bindConfirmLinks() {
        document.querySelectorAll('[data-cv-confirm][href]').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                var href = link.getAttribute('href');
                var opts = readConfirmOptions(link);

                confirm(opts).then(function (ok) {
                    if (ok) {
                        window.location.href = href;
                    }
                });
            });
        });
    }

    function bindConfirmForms() {
        document.querySelectorAll('form[data-cv-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (form.dataset.cvConfirmed === '1') {
                    form.dataset.cvConfirmed = '';
                    return;
                }

                e.preventDefault();
                var opts = readConfirmOptions(form);

                confirm(opts).then(function (ok) {
                    if (ok) {
                        form.dataset.cvConfirmed = '1';
                        form.submit();
                    }
                });
            });
        });
    }

    function showFlashMessages() {
        var node = document.getElementById('cv-flash-data');
        if (!node) return;

        var raw = node.textContent;
        node.parentNode.removeChild(node);

        try {
            var messages = JSON.parse(raw);
            if (!Array.isArray(messages) || !messages.length) return;

            var index = 0;
            function showNext() {
                if (index >= messages.length) return;
                var item = messages[index++];
                alert({
                    type: item.type === 'success' ? 'success' : 'error',
                    title: item.title,
                    message: item.message,
                    confirmText: 'Entendido',
                    onClose: showNext
                });
            }
            showNext();
        } catch (err) {
            /* ignore */
        }
    }

    function init() {
        bindConfirmLinks();
        bindConfirmForms();
        showFlashMessages();
    }

    window.CvDialog = {
        alert: alert,
        confirm: confirm,
        init: init
    };

    document.addEventListener('DOMContentLoaded', init);
})(window);
