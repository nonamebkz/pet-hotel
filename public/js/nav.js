(function () {
    'use strict';

    function setMobileIcons(nav, isOpen) {
        var openIcon = nav.querySelector('[data-nav-icon-open]');
        var closeIcon = nav.querySelector('[data-nav-icon-close]');
        var trigger = nav.querySelector('[data-nav-mobile-trigger]');

        if (openIcon) {
            openIcon.classList.toggle('hidden', isOpen);
        }

        if (closeIcon) {
            closeIcon.classList.toggle('hidden', !isOpen);
        }

        if (trigger) {
            trigger.setAttribute('aria-label', isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi');
        }
    }

    function closeAllDropdowns(except) {
        document.querySelectorAll('[data-nav-dropdown]').forEach(function (dropdown) {
            if (dropdown === except) {
                return;
            }

            var panel = dropdown.querySelector('[data-nav-dropdown-panel]');
            var trigger = dropdown.querySelector('[data-nav-dropdown-trigger]');

            if (panel) {
                panel.classList.add('hidden');
            }

            if (trigger) {
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    function closeMobileMenu(nav, restoreFocus) {
        var panel = nav.querySelector('[data-nav-mobile-panel]');
        var trigger = nav.querySelector('[data-nav-mobile-trigger]');

        if (panel) {
            panel.classList.add('hidden');
        }

        if (trigger) {
            trigger.setAttribute('aria-expanded', 'false');
            if (restoreFocus) {
                trigger.focus();
            }
        }

        setMobileIcons(nav, false);
        closeAllDropdowns(null);
    }

    document.querySelectorAll('[data-nav]').forEach(function (nav) {
        nav.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        var mobileTrigger = nav.querySelector('[data-nav-mobile-trigger]');
        var mobilePanel = nav.querySelector('[data-nav-mobile-panel]');

        if (mobileTrigger && mobilePanel) {
            mobileTrigger.addEventListener('click', function (event) {
                event.stopPropagation();
                var isOpen = !mobilePanel.classList.contains('hidden');

                if (isOpen) {
                    closeMobileMenu(nav, false);
                } else {
                    mobilePanel.classList.remove('hidden');
                    mobileTrigger.setAttribute('aria-expanded', 'true');
                    setMobileIcons(nav, true);
                    closeAllDropdowns(null);
                }
            });
        }

        nav.querySelectorAll('[data-nav-dropdown]').forEach(function (dropdown) {
            var trigger = dropdown.querySelector('[data-nav-dropdown-trigger]');
            var panel = dropdown.querySelector('[data-nav-dropdown-panel]');

            if (!trigger || !panel) {
                return;
            }

            trigger.addEventListener('click', function (event) {
                event.stopPropagation();
                var isOpen = !panel.classList.contains('hidden');

                closeAllDropdowns(isOpen ? null : dropdown);

                if (isOpen) {
                    panel.classList.add('hidden');
                    trigger.setAttribute('aria-expanded', 'false');
                } else {
                    panel.classList.remove('hidden');
                    trigger.setAttribute('aria-expanded', 'true');
                }
            });
        });
    });

    document.addEventListener('click', function (event) {
        closeAllDropdowns(null);
        document.querySelectorAll('[data-nav]').forEach(function (nav) {
            var panel = nav.querySelector('[data-nav-mobile-panel]');
            var trigger = nav.querySelector('[data-nav-mobile-trigger]');
            if (!panel || panel.classList.contains('hidden')) {
                return;
            }
            if (trigger && (event.target === trigger || trigger.contains(event.target))) {
                return;
            }
            if (panel.contains(event.target)) {
                return;
            }
            closeMobileMenu(nav, true);
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }

        closeAllDropdowns(null);
        document.querySelectorAll('[data-nav]').forEach(function (nav) {
            closeMobileMenu(nav, true);
        });
    });
})();
