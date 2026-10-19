import HSDropdown from 'preline/plugins/dropdown-non-auto';
import HSOverlay from 'preline/plugins/overlay-non-auto';
import HSCollapse from 'preline/plugins/collapse-non-auto';
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
 * Graphique en aires « à la shadcn » : data-area-chart='{"labels":[…],"series":[{"name":"…","data":[…]}]}'
 */
async function mountAreaCharts() {
    const charts = document.querySelectorAll('[data-area-chart]');
    if (charts.length === 0) {
        return;
    }

    // Chargé à la demande : seules les pages avec un graphique paient son poids.
    const { default: ApexCharts } = await import('apexcharts');
    const css = getComputedStyle(document.documentElement);
    const color = (name) => css.getPropertyValue(name).trim();

    charts.forEach((el) => {
        const { labels, series } = JSON.parse(el.dataset.areaChart);

        new ApexCharts(el, {
            chart: { type: 'area', height: 260, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'inherit', parentHeightOffset: 0 },
            series,
            colors: [color('--chart-1'), color('--chart-2')],
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 95] } },
            dataLabels: { enabled: false },
            grid: { borderColor: color('--border'), strokeDashArray: 4, padding: { left: 8, right: 8 }, yaxis: { lines: { show: true } } },
            xaxis: { type: 'category', categories: labels, tickAmount: 7, axisBorder: { show: false }, axisTicks: { show: false }, labels: { rotate: 0, hideOverlappingLabels: true, style: { colors: color('--muted-foreground'), fontSize: '12px' } }, tooltip: { enabled: false } },
            yaxis: { show: false, min: 0 },
            legend: { show: false },
            tooltip: { theme: 'light', x: { show: true } },
        }).render();
    });
}

/**
 * Comportements temps réel, branchés par attributs data-* :
 * - data-live-conversation="{id}" : ajoute les messages reçus
 * - data-live-alerts / data-live-queue : recharge la page à chaque événement
 * - data-autoscroll : garde le fil de discussion en bas
 */
function mountRealtime() {
    document.querySelectorAll('[data-autoscroll]').forEach((el) => {
        el.scrollTop = el.scrollHeight;
    });

    if (!window.Echo) {
        return;
    }

    document.querySelectorAll('[data-live-conversation]').forEach((thread) => {
        const template = document.getElementById('message-template');

        window.Echo.private(`conversation.${thread.dataset.liveConversation}`).listen('.message.sent', ({ message }) => {
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
}

document.addEventListener('DOMContentLoaded', () => {
    HSDropdown.autoInit();
    HSOverlay.autoInit();
    HSCollapse.autoInit();
    mountAreaCharts();
    mountRealtime();
});
