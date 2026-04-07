const CACHE_NAME = 'apex-operator-v1';

// File yang di-cache saat install (App Shell)
const APP_SHELL = [
    '/operator',
    '/operator/fit-to-work',
    '/operator/p2h',
    '/operator/timesheet',
    '/manifest.json',
    '/assets/images/logo.png',
    '/assets/images/favicon.ico',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
    'https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css',
    'https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js',
];

// ── Install: cache app shell ──────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(APP_SHELL);
        }).then(() => self.skipWaiting())
    );
});

// ── Activate: hapus cache lama ───────────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            )
        ).then(() => self.clients.claim())
    );
});

// ── Fetch: Network First untuk POST (form submit), Cache First untuk aset ─────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // POST request (submit form) → selalu ke network, jangan cache
    if (request.method === 'POST') {
        event.respondWith(
            fetch(request).catch(() => {
                return new Response(
                    JSON.stringify({ error: 'Tidak ada koneksi internet. Silakan coba lagi saat online.' }),
                    { status: 503, headers: { 'Content-Type': 'application/json' } }
                );
            })
        );
        return;
    }

    // Hanya handle request dari origin yang sama + CDN
    const isSameOrigin = url.origin === self.location.origin;
    const isCDN = url.hostname.includes('jsdelivr.net');

    if (!isSameOrigin && !isCDN) return;

    // Halaman operator → Network First (agar selalu data terbaru), fallback cache
    if (isSameOrigin && url.pathname.startsWith('/operator')) {
        event.respondWith(
            fetch(request)
                .then(response => {
                    // Update cache dengan response terbaru
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                    return response;
                })
                .catch(() => caches.match(request).then(cached => {
                    if (cached) return cached;
                    return offlinePage();
                }))
        );
        return;
    }

    // Aset statis (CSS, JS, gambar) → Cache First
    event.respondWith(
        caches.match(request).then(cached => {
            if (cached) return cached;
            return fetch(request).then(response => {
                const clone = response.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                return response;
            });
        })
    );
});

// ── Halaman offline fallback ──────────────────────────────────────────────────
function offlinePage() {
    return new Response(`
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidak Ada Koneksi</title>
    <style>
        body { font-family: Inter, sans-serif; background: #0f172a; color: #fff;
               display: flex; flex-direction: column; align-items: center; justify-content: center;
               min-height: 100vh; margin: 0; text-align: center; padding: 2rem; }
        .icon { font-size: 4rem; margin-bottom: 1rem; }
        h2 { font-size: 1.4rem; margin-bottom: .5rem; }
        p  { color: rgba(255,255,255,.5); font-size: .9rem; margin-bottom: 1.5rem; }
        button { background: #dc2626; color: #fff; border: none; border-radius: 12px;
                 padding: .8rem 2rem; font-size: 1rem; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>
    <div class="icon">📡</div>
    <h2>Tidak Ada Koneksi Internet</h2>
    <p>Periksa koneksi Anda dan coba lagi.<br>Data yang sudah tersimpan masih bisa dilihat.</p>
    <button onclick="location.reload()">Coba Lagi</button>
</body>
</html>`, {
        headers: { 'Content-Type': 'text/html; charset=utf-8' }
    });
}
