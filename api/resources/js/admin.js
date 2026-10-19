import '@tabler/core/dist/js/tabler.esm.js';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

if (import.meta.env.VITE_REVERB_APP_KEY) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
    });
}

/**
 * Comportements déclaratifs du back-office, branchés par attributs data-* :
 * - data-live-conversation="{id}" : ajoute les messages reçus en temps réel
 * - data-live-alerts : recharge la page à chaque nouvelle alerte superviseur
 * - data-autoscroll : garde le fil de discussion en bas
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-autoscroll]').forEach((el) => {
        el.scrollTop = el.scrollHeight;
    });

    if (!window.Echo) {
        return;
    }

    document.querySelectorAll('[data-live-conversation]').forEach((thread) => {
        const id = thread.dataset.liveConversation;
        const template = document.getElementById('message-template');

        window.Echo.private(`conversation.${id}`).listen('.message.sent', ({ message }) => {
            if (thread.querySelector(`[data-message-id="${message.id}"]`)) {
                return;
            }
            const node = template.content.cloneNode(true);
            node.querySelector('[data-message-id]').dataset.messageId = message.id;
            node.querySelector('[data-body]').textContent = message.body;
            node.querySelector('[data-author]').textContent = message.sender?.display_name ?? 'SUNOOGO';
            node.querySelector('[data-time]').textContent = new Date(message.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            thread.appendChild(node);
            const scroller = thread.closest('[data-autoscroll]') ?? thread;
            scroller.scrollTop = scroller.scrollHeight;
        });
    });

    if (document.querySelector('[data-live-alerts]')) {
        window.Echo.private('supervisors').listen('.emergency.raised', () => window.location.reload());
    }

    if (document.querySelector('[data-live-queue]')) {
        window.Echo.private('listeners').listen('.conversation.updated', () => window.location.reload());
    }
});
