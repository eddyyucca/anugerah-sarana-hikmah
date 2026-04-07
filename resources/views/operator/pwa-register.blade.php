{{-- PWA Service Worker Register — include sebelum </body> semua halaman operator --}}
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('SW registered:', reg.scope))
            .catch(err => console.log('SW error:', err));
    });
}

// Install prompt — simpan event untuk tombol "Pasang Aplikasi"
let deferredPrompt;
window.addEventListener('beforeinstallprompt', e => {
    e.preventDefault();
    deferredPrompt = e;
    const btn = document.getElementById('btnInstallPwa');
    if (btn) btn.style.display = 'flex';
});

window.addEventListener('appinstalled', () => {
    deferredPrompt = null;
    const btn = document.getElementById('btnInstallPwa');
    if (btn) btn.style.display = 'none';
});

function installPwa() {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then(() => { deferredPrompt = null; });
}
</script>
