// Registro del Service Worker para PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        var appBase = (window.CV_APP_BASE || '/').replace(/\/?$/, '/');
        navigator.serviceWorker.register(appBase + 'sw.js?v=6')
            .then(function(reg) {
                if (reg.waiting) {
                    reg.waiting.postMessage({ type: 'SKIP_WAITING' });
                }
                reg.update();
            })
            .catch(function(err) {
                console.log('SW error:', err);
            });
    });
}

// Banner de instalación PWA
let deferredPrompt;
window.addEventListener('beforeinstallprompt', function(e) {
    e.preventDefault();
    deferredPrompt = e;

    if (!localStorage.getItem('pwa-dismissed')) {
        showInstallBanner();
    }
});

function showInstallBanner() {
    var banner = document.createElement('div');
    banner.className = 'pwa-install-banner';
    banner.innerHTML = '<span>Instalar app en tu dispositivo</span>' +
        '<button class="btn btn-light btn-sm ms-2" id="pwa-install-btn">Instalar</button>' +
        '<button class="btn btn-link btn-sm text-white" id="pwa-dismiss-btn">×</button>';
    banner.style.cssText = 'position:fixed;bottom:0;left:0;right:0;background:#1a5f2a;color:#fff;padding:12px 16px;display:flex;align-items:center;justify-content:center;gap:8px;z-index:9999;font-size:14px;';
    document.body.appendChild(banner);

    document.getElementById('pwa-install-btn').addEventListener('click', function() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function() {
                deferredPrompt = null;
                banner.remove();
            });
        }
    });

    document.getElementById('pwa-dismiss-btn').addEventListener('click', function() {
        localStorage.setItem('pwa-dismissed', '1');
        banner.remove();
    });
}
