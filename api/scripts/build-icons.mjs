/**
 * Génère resources/icons/tabler.php à partir de @tabler/icons (MIT) :
 * seules les icônes listées ici sont embarquées dans le site, en SVG inline.
 * Usage : npm run icons
 */
import { readFileSync, writeFileSync } from 'node:fs';

const icons = [
    'arrow-right', 'arrow-up-right', 'calendar-event', 'check', 'chevron-down', 'circle-check',
    'clock', 'device-mobile', 'eye-off', 'heart-handshake', 'lock', 'map-pin', 'menu-2',
    'message-circle', 'phone', 'phone-call', 'school', 'shield-check', 'sos', 'sparkles',
    'user-heart', 'users', 'video', 'x', 'brand-android', 'brand-apple', 'alert-triangle',
    'book', 'mood-smile', 'lifebuoy',
    // Back-office
    'layout-dashboard', 'messages', 'flag', 'calendar', 'stethoscope', 'receipt', 'tag', 'history',
    'logout', 'external-link', 'trending-up', 'trending-down', 'send', 'circle-x', 'trash', 'plus',
    'search', 'notes', 'arrow-left', 'layout-sidebar', 'headset', 'hourglass', 'dots-vertical',
    'user', 'list', 'calendar-plus', 'settings', 'circle-plus', 'alert-circle', 'loader-2',
    'selector', 'chevron-right', 'mail', 'pencil', 'player-pause', 'player-play', 'ban', 'circle-dot',
];

const entries = icons.map((name) => {
    const svg = readFileSync(`node_modules/@tabler/icons/icons/outline/${name}.svg`, 'utf8');
    const inner = svg
        .replace(/^[\s\S]*?>\s*/, '')
        .replace(/<\/svg>\s*$/, '')
        .replace(/<path stroke="none" d="M0 0h24v24H0z" fill="none" \/>\s*/, '')
        .replace(/\s+/g, ' ')
        .trim();

    return `    '${name}' => '${inner.replace(/'/g, "\\'")}',`;
});

writeFileSync(
    'resources/icons/tabler.php',
    `<?php\n\n// Généré par scripts/build-icons.mjs — ne pas modifier à la main.\n\nreturn [\n${entries.join('\n')}\n];\n`,
);

console.log(`${icons.length} icônes générées.`);
