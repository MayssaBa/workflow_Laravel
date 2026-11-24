(function () {
    'use strict';

    const storageKey = 'teamo-theme';

    function storedTheme() {
        try {
            return localStorage.getItem(storageKey);
        } catch (error) {
            return null;
        }
    }

    function rememberTheme(theme) {
        try {
            localStorage.setItem(storageKey, theme);
        } catch (error) {
            return;
        }
    }

    function systemTheme() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function resolvedTheme(preference) {
        if (preference === 'auto') {
            return systemTheme();
        }

        return preference === 'dark' ? 'dark' : 'light';
    }

    function currentTheme() {
        return document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
    }

    function updateToggleButtons(theme) {
        document.querySelectorAll('[data-teamo-theme-toggle]').forEach(function (button) {
            const isDark = theme === 'dark';
            const label = isDark ? 'Activer le mode clair' : 'Activer le mode sombre';
            const icon = button.querySelector('i');

            button.setAttribute('aria-label', label);
            button.setAttribute('title', label);
            button.setAttribute('aria-pressed', String(isDark));

            if (icon) {
                icon.classList.toggle('bi-sun', isDark);
                icon.classList.toggle('bi-moon-stars', !isDark);
            }
        });
    }

    function updateThemeControls(preference) {
        document.querySelectorAll('[data-teamo-theme-option]').forEach(function (radio) {
            radio.checked = radio.value === preference;
        });

        updateToggleButtons(resolvedTheme(preference));
    }

    function applyTheme(preference) {
        document.documentElement.setAttribute('data-bs-theme', resolvedTheme(preference));
        updateThemeControls(preference);
    }

    function initTheme() {
        const saved = storedTheme();
        const preference = saved === 'dark' || saved === 'light' || saved === 'auto' ? saved : 'auto';

        applyTheme(preference);

        document.querySelectorAll('[data-teamo-theme-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                const next = currentTheme() === 'dark' ? 'light' : 'dark';
                rememberTheme(next);
                applyTheme(next);
            });
        });

        document.querySelectorAll('[data-teamo-theme-option]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (!radio.checked) {
                    return;
                }

                rememberTheme(radio.value);
                applyTheme(radio.value);
            });
        });

        if (window.matchMedia) {
            const media = window.matchMedia('(prefers-color-scheme: dark)');
            const syncSystemTheme = function () {
                const current = storedTheme();
                if (current === 'auto' || current === null) {
                    applyTheme('auto');
                }
            };

            if (media.addEventListener) {
                media.addEventListener('change', syncSystemTheme);
            } else if (media.addListener) {
                media.addListener(syncSystemTheme);
            }
        }
    }

    function initPasswordToggles() {
        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(button.getAttribute('data-password-toggle'));
                if (!input) {
                    return;
                }

                const visible = input.type === 'password';
                input.type = visible ? 'text' : 'password';
                button.setAttribute('aria-pressed', String(visible));
                button.setAttribute('aria-label', visible ? 'Masquer le mot de passe' : 'Afficher le mot de passe');

                const icon = button.querySelector('i');
                if (icon) {
                    icon.classList.toggle('bi-eye', !visible);
                    icon.classList.toggle('bi-eye-slash', visible);
                }
            });
        });
    }

    function initLoadingButtons() {
        document.querySelectorAll('form[data-loading]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (form.dataset.loadingActive === 'true') {
                    event.preventDefault();
                    return;
                }

                const button = form.querySelector('[data-loading-button]') || form.querySelector('button[type="submit"]');
                if (!button || button.disabled) {
                    return;
                }

                form.dataset.loadingActive = 'true';
                button.disabled = true;

                const spinner = button.querySelector('[data-loading-spinner]');
                const label = button.querySelector('[data-loading-label]');

                if (spinner) {
                    spinner.classList.remove('d-none');
                }

                if (label) {
                    label.textContent = 'Envoi en cours…';
                }
            });
        });
    }

    function initPasswordStrength() {
        const input = document.querySelector('[data-password-strength-input]');
        const container = document.querySelector('[data-password-strength]');

        if (!input || !container) {
            return;
        }

        const segments = container.querySelectorAll('[data-strength-segment]');
        const rules = container.querySelectorAll('[data-strength-rule]');
        const label = container.querySelector('[data-strength-label]');
        const help = document.getElementById(input.id + '-help');
        const labels = ['', 'faible', 'moyen', 'bon', 'fort'];

        function update() {
            const value = input.value;
            const met = {
                length: value.length >= 8,
                uppercase: /[A-Z]/.test(value),
                digit: /\d/.test(value),
                symbol: /[^A-Za-z0-9]/.test(value),
            };
            const score = Object.values(met).filter(Boolean).length;

            container.setAttribute('data-level', String(score));
            segments.forEach(function (segment, index) {
                segment.classList.toggle('active', index < score);
            });
            rules.forEach(function (rule) {
                const key = rule.getAttribute('data-strength-rule');
                rule.classList.toggle('is-met', Boolean(met[key]));
            });

            if (label) {
                label.textContent = value.length > 0
                    ? 'Force du mot de passe : ' + labels[score]
                    : 'Force du mot de passe';
            }

            if (help) {
                help.textContent = value.length > 0
                    ? 'Force : ' + labels[score]
                    : 'Au moins 8 caractères, une majuscule, un chiffre et un symbole.';
            }
        }

        input.addEventListener('input', update);
        update();
    }

    function initPasswordConfirmation() {
        const confirmation = document.querySelector('[data-password-confirmation-input]');

        if (!confirmation) {
            return;
        }

        const password = document.getElementById(confirmation.dataset.passwordConfirmationTarget || 'password');
        const feedback = document.getElementById(confirmation.dataset.passwordConfirmationFeedback || confirmation.id + '-help');

        if (!feedback) {
            return;
        }

        const initialMessage = feedback.textContent.trim();

        function update() {
            if (!password || confirmation.value.length === 0) {
                feedback.textContent = initialMessage;
                feedback.classList.remove('text-success', 'text-danger');
                confirmation.classList.remove('is-valid');
                confirmation.removeAttribute('aria-invalid');
                return;
            }

            const matches = confirmation.value === password.value;

            feedback.textContent = matches
                ? 'Les mots de passe correspondent.'
                : 'Les mots de passe ne correspondent pas.';
            feedback.classList.toggle('text-success', matches);
            feedback.classList.toggle('text-danger', !matches);
            confirmation.classList.toggle('is-valid', matches);
            confirmation.classList.toggle('is-invalid', !matches);
            confirmation.setAttribute('aria-invalid', String(!matches));
        }

        confirmation.addEventListener('input', update);
        if (password) {
            password.addEventListener('input', update);
        }
        update();
    }

    function initAvatarPreview() {
        const input = document.querySelector('[data-avatar-preview-input]');
        const preview = document.querySelector('[data-avatar-preview]');
        const wrapper = document.querySelector('[data-avatar-preview-wrap]');

        if (!input || !preview || !wrapper) {
            return;
        }

        const initialSource = preview.getAttribute('src');

        function restore() {
            if (initialSource) {
                preview.setAttribute('src', initialSource);
                wrapper.hidden = false;
            } else {
                wrapper.hidden = true;
            }
        }

        input.addEventListener('change', function () {
            const file = input.files && input.files.length > 0 ? input.files[0] : null;

            if (!file || file.type.indexOf('image/') !== 0) {
                restore();
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                preview.setAttribute('src', String(event.target.result));
                wrapper.hidden = false;
            };
            reader.readAsDataURL(file);
        });

        document.addEventListener('teamo:form-reset', function (event) {
            if (event.detail && event.detail.form && event.detail.form.contains(input)) {
                input.value = '';
                restore();
            }
        });
    }

    function initUnsavedWarnings() {
        const forms = document.querySelectorAll('form[data-unsaved-warning]');

        function snapshot(form) {
            return JSON.stringify(Array.from(new FormData(form).entries()));
        }

        forms.forEach(function (form) {
            form.dataset.unsavedInitial = snapshot(form);
            form.dataset.unsavedDirty = 'false';
            form.dataset.unsavedSubmitted = 'false';

            function update() {
                form.dataset.unsavedDirty = snapshot(form) !== form.dataset.unsavedInitial ? 'true' : 'false';
            }

            form.addEventListener('input', update);
            form.addEventListener('change', update);

            form.addEventListener('submit', function () {
                form.dataset.unsavedSubmitted = 'true';
                form.dataset.unsavedInitial = snapshot(form);
                form.dataset.unsavedDirty = 'false';
            });

            form.querySelectorAll('[data-unsaved-reset]').forEach(function (button) {
                button.addEventListener('click', function () {
                    form.reset();
                    form.dataset.unsavedInitial = snapshot(form);
                    form.dataset.unsavedDirty = 'false';
                    form.dataset.unsavedSubmitted = 'false';
                    form.querySelectorAll('.is-invalid, .is-valid').forEach(function (field) {
                        field.classList.remove('is-invalid', 'is-valid');
                        field.removeAttribute('aria-invalid');
                    });
                    document.dispatchEvent(new CustomEvent('teamo:form-reset', { detail: { form: form } }));
                });
            });
        });

        window.addEventListener('beforeunload', function (event) {
            const hasChanges = Array.from(forms).some(function (form) {
                return form.dataset.unsavedDirty === 'true' && form.dataset.unsavedSubmitted !== 'true';
            });

            if (hasChanges) {
                event.preventDefault();
                event.returnValue = '';
                return '';
            }
        });
    }

    function initProfileTabs() {
        const nav = document.querySelector('[data-teamo-profile-tabs]');

        if (!nav || !window.bootstrap || !window.bootstrap.Tab) {
            return;
        }

        const tabs = nav.querySelectorAll('[data-bs-toggle="pill"]');
        const requested = nav.dataset.teamoActiveTab || (window.location.hash ? window.location.hash.slice(1) : '');
        const requestedTab = Array.from(tabs).find(function (tab) {
            return tab.getAttribute('data-bs-target') === '#' + requested || tab.id === requested + '-tab';
        });

        if (requestedTab) {
            new window.bootstrap.Tab(requestedTab).show();
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('shown.bs.tab', function () {
                const target = tab.getAttribute('data-bs-target').replace('#', '');
                window.history.replaceState(null, '', '#' + target);
            });
        });

        const editButton = document.querySelector('[data-teamo-profile-edit]');

        if (editButton) {
            editButton.addEventListener('click', function () {
                const informationTab = nav.querySelector('[data-bs-target="#informations"]');

                if (informationTab) {
                    new window.bootstrap.Tab(informationTab).show();
                    const firstField = document.getElementById('name');
                    if (firstField) {
                        firstField.focus();
                    }
                }
            });
        }
    }

    function initErrorModals() {
        document.querySelectorAll('[data-teamo-modal][data-teamo-has-errors="true"]').forEach(function (element) {
            if (window.bootstrap && window.bootstrap.Modal) {
                new window.bootstrap.Modal(element).show();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initTheme();
        initPasswordToggles();
        initPasswordStrength();
        initPasswordConfirmation();
        initAvatarPreview();
        initUnsavedWarnings();
        initProfileTabs();
        initLoadingButtons();
        initErrorModals();
    });
})();
