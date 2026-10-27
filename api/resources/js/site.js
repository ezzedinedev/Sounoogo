// Seuls les plugins Preline utilisés par le site sont chargés, initialisés explicitement.
import HSAccordion from 'preline/plugins/accordion-non-auto';
import HSCollapse from 'preline/plugins/collapse-non-auto';
import HSTabs from 'preline/plugins/tabs-non-auto';

const initIntro = () => {
    const intro = document.getElementById('sunoogo-intro');
    const shell = document.getElementById('sunoogo-site-shell');
    const root = document.documentElement;

    if (!intro || !root.classList.contains('sunoogo-intro-pending')) return;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const skip = document.getElementById('sunoogo-intro-skip');
    const main = document.getElementById('contenu');
    let finished = false;
    let timeout;

    document.body.classList.add('sunoogo-intro-active');
    shell?.setAttribute('inert', '');
    skip?.focus({ preventScroll: true });

    const finish = () => {
        if (finished) return;
        finished = true;
        window.clearTimeout(timeout);
        intro.classList.add('is-exiting');

        const cleanup = () => {
            intro.remove();
            root.classList.remove('sunoogo-intro-pending');
            shell?.removeAttribute('inert');
            document.body.classList.remove('sunoogo-intro-active');
            document.removeEventListener('keydown', onKeyDown);
            main?.focus({ preventScroll: true });
        };

        if (reducedMotion) cleanup();
        else window.setTimeout(cleanup, 420);
    };

    const onKeyDown = (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            finish();
            return;
        }

        if (event.key === 'Tab') {
            event.preventDefault();
            skip?.focus({ preventScroll: true });
        }
    };

    skip?.addEventListener('click', finish);
    document.addEventListener('keydown', onKeyDown);
    timeout = window.setTimeout(finish, reducedMotion ? 800 : 2350);
};

const initPageCurtain = () => {
    const curtain = document.getElementById('sunoogo-page-curtain');
    const root = document.documentElement;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!curtain) return;

    if (reducedMotion || document.body.dataset.sunoogoMotion === 'off') {
        root.classList.remove('sunoogo-curtain-arriving');
        return;
    }

    if (root.classList.contains('sunoogo-curtain-arriving')) {
        window.requestAnimationFrame(() => curtain.classList.add('is-releasing'));
        window.setTimeout(() => {
            root.classList.remove('sunoogo-curtain-arriving');
            curtain.classList.remove('is-releasing');
        }, 900);
    }

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');

        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        if ((link.target && link.target !== '_self') || link.hasAttribute('download')) return;

        const url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin || (url.pathname === window.location.pathname && url.search === window.location.search)) return;

        try {
            window.sessionStorage.setItem('sunoogo-curtain', '1');
        } catch (_) {
            return;
        }

        event.preventDefault();
        curtain.classList.add('is-entering');
        window.setTimeout(() => window.location.assign(url.href), 650);
    });

    window.addEventListener('pageshow', (event) => {
        if (!event.persisted) return;
        root.classList.remove('sunoogo-curtain-arriving');
        curtain.classList.remove('is-entering', 'is-releasing');
    });
};

const initMotion = () => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const motionDisabled = document.body.dataset.sunoogoMotion === 'off';

    if (reducedMotion || motionDisabled) return;

    const progress = document.querySelector('[data-sunoogo-scroll-progress] > span');
    const header = document.querySelector('[data-sunoogo-sticky-header]');
    const footer = document.querySelector('[data-sunoogo-footer]');
    const footerSignature = document.querySelector('[data-sunoogo-footer-signature]');
    let progressFrame = 0;
    let previousScrollY = window.scrollY;

    const updateProgress = () => {
        if (!progress || progressFrame) return;

        progressFrame = window.requestAnimationFrame(() => {
            const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
            const ratio = maxScroll > 0 ? Math.min(1, Math.max(0, window.scrollY / maxScroll)) : 0;
            const scrollY = window.scrollY;

            progress.style.transform = `scaleX(${ratio})`;

            if (header) {
                const menu = document.getElementById('nav-menu');
                const mobileMenuOpen = window.matchMedia('(max-width: 1199px)').matches && menu && !menu.classList.contains('hidden');
                const focusInsideHeader = header.contains(document.activeElement);
                const movingDown = scrollY > previousScrollY + 2;
                const movingUp = scrollY < previousScrollY - 2;

                if (scrollY < 160 || movingUp || mobileMenuOpen || focusInsideHeader) header.classList.remove('is-hidden');
                else if (movingDown) header.classList.add('is-hidden');
            }

            if (footer && footerSignature) {
                const bounds = footer.getBoundingClientRect();
                const reveal = Math.min(1, Math.max(0, (window.innerHeight - bounds.top) / Math.max(bounds.height, 1)));
                footerSignature.style.opacity = `${0.2 + reveal * 0.8}`;
                footerSignature.style.transform = `translate3d(0, ${(1 - reveal) * 35}%, 0)`;
            }

            previousScrollY = scrollY;
            progressFrame = 0;
        });
    };

    window.addEventListener('scroll', updateProgress, { passive: true });
    window.addEventListener('resize', updateProgress, { passive: true });
    updateProgress();

    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    if (finePointer) {
        document.querySelectorAll('[data-sunoogo-hero] .sunoogo-hero-light').forEach((heroLight) => {
            const hero = heroLight.parentElement;
            let pointerFrame = 0;
            let point = null;

            hero.addEventListener('pointermove', (event) => {
                point = event;
                if (pointerFrame) return;

                pointerFrame = window.requestAnimationFrame(() => {
                    const bounds = hero.getBoundingClientRect();
                    hero.style.setProperty('--sunoogo-pointer-x', `${point.clientX - bounds.left}px`);
                    hero.style.setProperty('--sunoogo-pointer-y', `${point.clientY - bounds.top}px`);
                    hero.classList.add('is-pointer-active');
                    pointerFrame = 0;
                });
            }, { passive: true });

            hero.addEventListener('pointerleave', () => hero.classList.remove('is-pointer-active'), { passive: true });
        });

        document.querySelectorAll('[data-sunoogo-tilt]:not(.sunoogo-page-hero__visual--urgent) .sunoogo-page-hero__card').forEach((card) => {
            const visual = card.parentElement;

            visual.addEventListener('pointermove', (event) => {
                const bounds = visual.getBoundingClientRect();
                const x = (event.clientX - bounds.left) / bounds.width - 0.5;
                const y = (event.clientY - bounds.top) / bounds.height - 0.5;

                card.style.animation = 'none';
                card.style.transform = `perspective(900px) rotateX(${y * -7}deg) rotateY(${x * 8}deg) translateY(-2px)`;
            }, { passive: true });

            visual.addEventListener('pointerleave', () => {
                card.style.removeProperty('animation');
                card.style.removeProperty('transform');
            }, { passive: true });
        });
    }

    if (!('IntersectionObserver' in window)) return;

    const revealTargets = [...document.querySelectorAll('main > section')].filter((section) => {
        const bounds = section.getBoundingClientRect();

        return bounds.top >= window.innerHeight && !(section.id && section.id === window.location.hash.slice(1));
    });

    if (!revealTargets.length) return;

    const reveal = (section) => {
        if (section.dataset.sunoogoMotion !== 'pending') return;

        section.dataset.sunoogoMotion = 'visible';
        observer.unobserve(section);

        window.setTimeout(() => {
            delete section.dataset.sunoogoMotion;
            section.querySelectorAll('[data-sunoogo-motion-item]').forEach((item) => {
                delete item.dataset.sunoogoMotionItem;
                item.style.removeProperty('--sunoogo-motion-delay');
            });
        }, 1100);
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) reveal(entry.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -48px 0px' });

    revealTargets.forEach((section) => {
        section.dataset.sunoogoMotion = 'pending';

        [...section.querySelectorAll('.grid > *')].forEach((item, index) => {
            item.dataset.sunoogoMotionItem = '';
            item.style.setProperty('--sunoogo-motion-delay', `${Math.min(index, 4) * 65}ms`);
        });

        observer.observe(section);
    });

    document.addEventListener('focusin', (event) => {
        const section = event.target.closest('[data-sunoogo-motion="pending"]');
        if (section) reveal(section);
    });

    window.addEventListener('hashchange', () => {
        const target = document.getElementById(window.location.hash.slice(1));
        const section = target?.closest('main > section');
        if (section) reveal(section);
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initIntro();
    initPageCurtain();
    HSCollapse.autoInit();
    HSAccordion.autoInit();
    HSTabs.autoInit();
    initMotion();
});
