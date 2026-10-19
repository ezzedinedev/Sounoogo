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
 * Histogramme : data-bar-chart='{"labels":[…],"series":[{"name":"…","data":[…]}],"unit":" F"}'.
 * Plusieurs séries s'empilent (ex. répartition par thème et par mois).
 */
async function mountBarCharts() {
    const charts = document.querySelectorAll('[data-bar-chart]');
    if (charts.length === 0) {
        return;
    }

    const { default: ApexCharts } = await import('apexcharts');
    const css = getComputedStyle(document.documentElement);
    const color = (name) => css.getPropertyValue(name).trim();
    const format = new Intl.NumberFormat('fr-FR');

    charts.forEach((el) => {
        const { labels, series, unit = '' } = JSON.parse(el.dataset.barChart);

        new ApexCharts(el, {
            chart: { type: 'bar', height: el.clientHeight || 240, stacked: series.length > 1, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'inherit', parentHeightOffset: 0, animations: { speed: 500 } },
            series,
            colors: [1, 2, 3, 4, 5].map((n) => color(`--chart-${n}`)),
            plotOptions: { bar: { borderRadius: 4, borderRadiusApplication: 'end', columnWidth: '52%' } },
            dataLabels: { enabled: false },
            grid: { borderColor: color('--border'), strokeDashArray: 4, padding: { left: 8, right: 8 } },
            xaxis: { categories: labels, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: color('--muted-foreground'), fontSize: '12px' } } },
            yaxis: { show: false, min: 0 },
            legend: { show: series.length > 1, position: 'bottom', fontSize: '12px', markers: { size: 5 } },
            tooltip: { theme: 'light', y: { formatter: (value) => `${format.format(value)}${unit}` } },
            states: { hover: { filter: { type: 'darken', value: 0.92 } } },
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

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/** « 45 s », « 12 min », « 1 h 05 ». */
function formatElapsed(seconds) {
    if (seconds < 60) return `${Math.floor(seconds)} s`;
    if (seconds < 3600) return `${Math.floor(seconds / 60)} min`;
    const hours = Math.floor(seconds / 3600);
    return `${hours} h ${String(Math.floor((seconds % 3600) / 60)).padStart(2, '0')}`;
}

/**
 * Poste de supervision : le fragment data-supervision="{url}" est rechargé à chaque événement temps réel
 * (et toutes les 15 s en filet de sécurité) ; les durées data-since défilent à la seconde.
 */
function mountSupervision() {
    const root = document.querySelector('[data-supervision]');
    if (!root) {
        return;
    }

    const tick = () => root.querySelectorAll('[data-since]').forEach((el) => {
        const seconds = Math.max(0, (Date.now() - Date.parse(el.dataset.since)) / 1000);
        el.textContent = formatElapsed(seconds);
        el.classList.toggle('text-destructive', Boolean(el.dataset.warnAfter) && seconds > Number(el.dataset.warnAfter));
    });

    let pending = null;
    const refresh = () => {
        clearTimeout(pending);
        pending = setTimeout(async () => {
            const response = await fetch(root.dataset.supervision, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).catch(() => null);
            if (response?.ok) {
                root.innerHTML = await response.text();
                tick();
            }
        }, 250);
    };

    const title = document.title;
    const notify = () => {
        refresh();
        document.title = `● Nouvelle alerte · ${title}`;
        setTimeout(() => { document.title = title; }, 15000);
    };

    tick();
    setInterval(tick, 1000);
    setInterval(refresh, 15000);
    window.Echo?.private('supervisors').listen('.emergency.raised', notify);
    window.Echo?.private('listeners').listen('.conversation.updated', refresh);
}

/** Copilote de l'écoutant : propositions insérées dans la zone de réponse, à relire avant envoi. */
function mountCopilot() {
    const card = document.querySelector('[data-copilot]');
    if (!card) {
        return;
    }
    const output = card.querySelector('[data-copilot-output]');
    const composer = document.getElementById('body');

    const element = (tag, className, text) => {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    };

    const show = (...nodes) => output.replaceChildren(...nodes);

    card.querySelectorAll('[data-copilot-action]').forEach((button) => {
        button.addEventListener('click', async () => {
            const action = button.dataset.copilotAction;
            const url = action === 'suggest' ? card.dataset.suggestUrl : card.dataset.summaryUrl;
            button.disabled = true;
            show(element('p', 'text-muted-foreground animate-pulse', 'Le copilote réfléchit…'));

            try {
                const response = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf(), Accept: 'application/json' } });
                const data = await response.json();
                if (!response.ok) {
                    show(element('p', 'text-destructive', data.message ?? 'Le copilote ne répond pas.'));
                    return;
                }

                if (action === 'suggest') {
                    show(...data.suggestions.map((text) => {
                        const option = element('button', 'rounded-lg border bg-background px-3 py-2.5 text-start leading-relaxed transition-colors hover:border-primary hover:bg-primary-soft/40', text);
                        option.type = 'button';
                        option.title = 'Utiliser dans ma réponse';
                        option.addEventListener('click', () => {
                            if (composer) {
                                composer.value = text;
                                composer.focus();
                            }
                        });
                        return option;
                    }), element('p', 'text-xs text-muted-foreground', 'Clique sur une proposition pour la reprendre dans ta réponse.'));
                } else {
                    const list = element('ul', 'mt-1 grid gap-1 text-muted-foreground');
                    data.watch_points.forEach((point) => list.appendChild(element('li', 'flex gap-2', `• ${point}`)));
                    show(element('p', 'leading-relaxed', data.summary), ...(data.watch_points.length ? [element('p', 'mt-2 font-medium', 'Points de vigilance'), list] : []));
                }
            } catch {
                show(element('p', 'text-destructive', 'Connexion impossible. Réessaie dans un instant.'));
            } finally {
                button.disabled = false;
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    HSDropdown.autoInit();
    HSOverlay.autoInit();
    HSCollapse.autoInit();
    mountAreaCharts();
    mountBarCharts();
    mountRealtime();
    mountSupervision();
    mountCopilot();
});
