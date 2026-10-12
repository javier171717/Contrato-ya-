const CACHE_NAME = 'contratoya-v6';
const BASE = new URL('./', self.location.href).pathname;

const STATIC_ASSETS = [
    BASE + 'css/app.css',
    BASE + 'css/cv-dialog.css',
    BASE + 'css/recibo.css',
    BASE + 'js/app.js',
    BASE + 'js/cv-dialog.js',
    BASE + 'js/pwa.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
];

function esRecursoEstatico(url) {
    return /\.(css|js|png|jpg|jpeg|webp|svg|ico|woff2?)$/i.test(url.pathname)
        || url.pathname.endsWith('/manifest.json')
        || url.pathname.endsWith('/manifest.php');
}

function esIconoMarca(url) {
    const p = url.pathname;
    return /favicon|apple-touch-icon|\/icons\//i.test(p);
}

function esPaginaHtml(request) {
    return request.mode === 'navigate'
        || (request.headers.get('accept') || '').includes('text/html');
}

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    if (esPaginaHtml(event.request) || !esRecursoEstatico(url)) {
        if (!esRecursoEstatico(url)) {
            event.respondWith(fetch(event.request));
            return;
        }
    }

    // Iconos y favicon: siempre red primero (evita quedarse con el logo viejo)
    if (esIconoMarca(url)) {
        event.respondWith(
            fetch(event.request).then((response) => {
                if (response && response.status === 200) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                }
                return response;
            }).catch(() => caches.match(event.request))
        );
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => {
            if (cached) return cached;

            return fetch(event.request).then((response) => {
                if (response && response.status === 200) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                }
                return response;
            });
        })
    );
});
