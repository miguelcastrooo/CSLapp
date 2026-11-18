import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const notificationSettings = window.schoolNotifications ?? {};
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    // Evita que Apache busque /broadcasting/auth fuera de la carpeta del proyecto.
    authEndpoint: `${window.location.origin}${notificationSettings.broadcastAuthPath ?? '/broadcasting/auth'}`,
    auth: {
        headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
    },
});
