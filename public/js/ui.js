(function () {
    'use strict';

    function closeActionMenus(except) {
        document.querySelectorAll('[data-action-menu]').forEach(function (menu) {
            if (menu === except) {
                return;
            }
            var panel = menu.querySelector('[data-action-menu-panel]');
            var trigger = menu.querySelector('[data-action-menu-trigger]');
            if (panel) {
                panel.classList.add('hidden');
            }
            if (trigger) {
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    document.querySelectorAll('[data-action-menu-trigger]').forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
            event.stopPropagation();
            var menu = trigger.closest('[data-action-menu]');
            var panel = menu ? menu.querySelector('[data-action-menu-panel]') : null;
            if (!panel) {
                return;
            }
            var isOpen = !panel.classList.contains('hidden');
            closeActionMenus(isOpen ? null : menu);
            if (isOpen) {
                panel.classList.add('hidden');
                trigger.setAttribute('aria-expanded', 'false');
            } else {
                panel.classList.remove('hidden');
                trigger.setAttribute('aria-expanded', 'true');
            }
        });
    });

    document.addEventListener('click', function () {
        closeActionMenus(null);
    });

    var modal = document.getElementById('ui-confirm-modal');
    var modalMessage = document.getElementById('ui-confirm-message');
    var modalOk = document.getElementById('ui-confirm-ok');
    var modalCancel = document.getElementById('ui-confirm-cancel');
    var pendingForm = null;

    function closeModal() {
        if (!modal) {
            return;
        }
        modal.classList.add('hidden');
        pendingForm = null;
    }

    if (modal && modalOk && modalCancel) {
        modalCancel.addEventListener('click', closeModal);
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });
        modalOk.addEventListener('click', function () {
            if (pendingForm) {
                pendingForm.submit();
            }
            closeModal();
        });
    }

    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.confirmed === '1') {
                form.dataset.confirmed = '';
                return;
            }
            event.preventDefault();
            if (!modal || !modalMessage) {
                if (window.confirm(form.getAttribute('data-confirm') || 'Lanjutkan?')) {
                    form.submit();
                }
                return;
            }
            modalMessage.textContent = form.getAttribute('data-confirm') || 'Lanjutkan aksi ini?';
            pendingForm = form;
            modal.classList.remove('hidden');
        });
    });

    document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var inputId = btn.getAttribute('data-password-toggle');
            var input = inputId ? document.getElementById(inputId) : null;
            if (!input) {
                return;
            }
            var isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.textContent = isHidden ? 'Sembunyikan' : 'Tampilkan';
        });
    });

    function scorePassword(value) {
        var score = 0;
        if (value.length >= 8) {
            score += 1;
        }
        if (value.length >= 12) {
            score += 1;
        }
        if (/[A-Z]/.test(value)) {
            score += 1;
        }
        if (/[0-9]/.test(value)) {
            score += 1;
        }
        if (/[^A-Za-z0-9]/.test(value)) {
            score += 1;
        }
        return score;
    }

    document.querySelectorAll('[data-password-strength]').forEach(function (input) {
        var meterId = input.getAttribute('data-password-strength');
        var meter = meterId ? document.getElementById(meterId) : null;
        var label = meter ? meter.querySelector('[data-strength-label]') : null;
        var bar = meter ? meter.querySelector('[data-strength-bar]') : null;

        input.addEventListener('input', function () {
            if (!meter || !bar || !label) {
                return;
            }
            var score = scorePassword(input.value);
            var widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
            var colors = ['bg-gray-200', 'bg-red-500', 'bg-primary', 'bg-yellow-500', 'bg-lime-500', 'bg-green-600'];
            var labels = ['', 'Lemah', 'Cukup', 'Sedang', 'Kuat', 'Sangat kuat'];
            bar.style.width = widths[score] || '0%';
            bar.className = 'h-1.5 rounded-full transition-all ' + (colors[score] || 'bg-gray-200');
            label.textContent = labels[score] || '';
        });
    });

    document.querySelectorAll('[data-password-match]').forEach(function (confirmInput) {
        var targetId = confirmInput.getAttribute('data-password-match');
        var target = targetId ? document.getElementById(targetId) : null;
        var field = confirmInput.closest('[data-password-field]');
        var hint = field
            ? field.querySelector('[data-match-hint]')
            : (confirmInput.parentElement ? confirmInput.parentElement.querySelector('[data-match-hint]') : null);

        function validate() {
            if (!target || !hint) {
                return;
            }
            if (confirmInput.value === '') {
                hint.textContent = '';
                hint.className = 'text-xs mt-1.5 text-content-secondary';
                return;
            }
            var matched = confirmInput.value === target.value;
            hint.textContent = matched ? 'Password cocok' : 'Password tidak cocok';
            hint.className = matched
                ? 'text-xs mt-1.5 text-success font-medium'
                : 'text-xs mt-1.5 text-red-600 font-medium';
        }

        confirmInput.addEventListener('input', validate);
        if (target) {
            target.addEventListener('input', validate);
        }
    });

    document.querySelectorAll('[data-generate-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-generate-password');
            var confirmId = btn.getAttribute('data-generate-password-confirm');
            var target = targetId ? document.getElementById(targetId) : null;
            var confirm = confirmId ? document.getElementById(confirmId) : null;
            var chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$';
            var password = '';
            for (var i = 0; i < 12; i += 1) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            if (target) {
                target.value = password;
                target.dispatchEvent(new Event('input'));
            }
            if (confirm) {
                confirm.value = password;
                confirm.dispatchEvent(new Event('input'));
            }
        });
    });

    document.querySelectorAll('form[data-loading-submit]').forEach(function (form) {
        form.addEventListener('submit', function () {
            var btn = form.querySelector('[type="submit"]');
            if (btn && !btn.disabled) {
                btn.disabled = true;
                btn.dataset.originalText = btn.textContent || '';
                btn.textContent = 'Memproses...';
            }
        });
    });

    document.querySelectorAll('[data-stepper]').forEach(function (stepper) {
        var steps = stepper.querySelectorAll('[data-step-panel]');
        var indicators = stepper.querySelectorAll('[data-step-indicator]');
        var startAttr = parseInt(stepper.getAttribute('data-step-start') || '0', 10);
        var current = Number.isFinite(startAttr) ? Math.max(0, Math.min(startAttr, steps.length - 1)) : 0;

        function setDotState(dot, state) {
            if (!dot) {
                return;
            }
            if (state === 'active') {
                dot.className = 'flex h-8 w-8 items-center justify-center rounded-xl bg-primary text-primary-foreground text-sm font-semibold shadow-sm';
            } else if (state === 'done') {
                dot.className = 'flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-600 text-white text-sm font-semibold shadow-sm';
            } else {
                dot.className = 'flex h-8 w-8 items-center justify-center rounded-xl bg-muted text-muted-foreground text-sm font-semibold';
            }
        }

        function showStep(index) {
            current = index;
            steps.forEach(function (panel, i) {
                panel.classList.toggle('hidden', i !== index);
            });
            indicators.forEach(function (indicator, i) {
                var active = i === index;
                var done = i < index;
                indicator.setAttribute('aria-current', active ? 'step' : 'false');
                setDotState(indicator.querySelector('[data-step-dot]'), active ? 'active' : (done ? 'done' : 'todo'));
                var label = indicator.querySelector('[data-step-label]');
                if (label) {
                    label.className = 'text-xs sm:text-sm ' + (active
                        ? 'font-semibold text-admin'
                        : (done ? 'font-medium text-success' : 'text-content-secondary'));
                }
            });
        }

        stepper.querySelectorAll('[data-step-next]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var panel = steps[current];
                if (!panel) {
                    return;
                }
                var fields = panel.querySelectorAll('input, select, textarea');
                for (var i = 0; i < fields.length; i += 1) {
                    if (!fields[i].checkValidity()) {
                        fields[i].reportValidity();
                        return;
                    }
                }
                if (current < steps.length - 1) {
                    showStep(current + 1);
                }
            });
        });

        stepper.querySelectorAll('[data-step-prev]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (current > 0) {
                    showStep(current - 1);
                }
            });
        });

        showStep(current);
    });

    document.querySelectorAll('[data-collapsible-trigger]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            var targetId = trigger.getAttribute('data-collapsible-trigger');
            var target = targetId ? document.getElementById(targetId) : null;
            if (target) {
                target.classList.toggle('hidden');
            }
        });
    });

    document.querySelectorAll('[data-lightbox]').forEach(function (img) {
        img.addEventListener('click', function () {
            var overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 cursor-pointer';
            var big = document.createElement('img');
            big.src = img.src;
            big.className = 'max-h-full max-w-full rounded-lg';
            overlay.appendChild(big);
            overlay.addEventListener('click', function () {
                overlay.remove();
            });
            document.body.appendChild(overlay);
        });
    });
})();
