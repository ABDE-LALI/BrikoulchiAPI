import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: process.env.VITE_REVERB_APP_KEY,
    wsHost: process.env.VITE_REVERB_HOST,
    wsPort: process.env.VITE_REVERB_PORT ?? 80,
    wssPort: process.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (process.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
