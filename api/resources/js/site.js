// Seuls les plugins Preline utilisés par le site sont chargés, initialisés explicitement.
import HSAccordion from 'preline/plugins/accordion-non-auto';
import HSCollapse from 'preline/plugins/collapse-non-auto';

document.addEventListener('DOMContentLoaded', () => {
    HSCollapse.autoInit();
    HSAccordion.autoInit();
});
