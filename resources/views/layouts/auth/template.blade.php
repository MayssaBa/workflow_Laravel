<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Teamo'))</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/brand/favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/brand/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/brand/apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/teamo.css') }}" rel="stylesheet">
    <script>
        try {
            const savedTheme = localStorage.getItem('teamo-theme');
            const preference = savedTheme === 'dark' || savedTheme === 'light' || savedTheme === 'auto'
                ? savedTheme
                : 'auto';
            const resolvedTheme = preference === 'auto'
                ? (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                : preference;
            document.documentElement.setAttribute('data-bs-theme', resolvedTheme);
        } catch (error) {
        }
    </script>
    @stack('styles')
</head>
<body class="teamo-auth-page">
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <aside class="col-lg-6 d-none d-lg-flex flex-column teamo-auth-showcase" aria-hidden="true">
                <div class="teamo-auth-showcase-top">
                    <img src="{{ asset('assets/brand/teamo-logo-dark.svg') }}" class="teamo-auth-proof-logo" alt="">
                </div>

                <div class="teamo-auth-showcase-body">
                    <h2 class="teamo-auth-headline fw-bold">Work better,<br>together.</h2>
                    <p class="teamo-auth-showcase-text">
                        Planifiez vos projets, suivez vos tâches et réunissez vos équipes au même endroit.
                    </p>

                    <svg class="teamo-auth-illustration" viewBox="0 0 420 260" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="366" cy="36" r="72" fill="#8091FF" opacity=".12"></circle>
                        <circle cx="44" cy="226" r="58" fill="#6478F5" opacity=".10"></circle>
                        <rect x="20" y="26" width="246" height="148" rx="18" fill="#FFFFFF" opacity=".10"></rect>
                        <rect x="44" y="50" width="116" height="10" rx="5" fill="#FFFFFF" opacity=".72"></rect>
                        <rect x="44" y="76" width="188" height="7" rx="3.5" fill="#FFFFFF" opacity=".28"></rect>
                        <rect x="44" y="94" width="164" height="7" rx="3.5" fill="#FFFFFF" opacity=".28"></rect>
                        <rect x="44" y="126" width="84" height="30" rx="9" fill="#6478F5" opacity=".55"></rect>
                        <circle cx="218" cy="141" r="16" fill="#C7D0FF"></circle>
                        <circle cx="244" cy="141" r="16" fill="#8091FF"></circle>
                        <rect x="186" y="92" width="196" height="132" rx="18" fill="#17243B" opacity=".72"></rect>
                        <rect x="210" y="118" width="104" height="9" rx="4.5" fill="#FFFFFF" opacity=".62"></rect>
                        <rect x="210" y="142" width="148" height="6" rx="3" fill="#FFFFFF" opacity=".22"></rect>
                        <rect x="210" y="158" width="126" height="6" rx="3" fill="#FFFFFF" opacity=".22"></rect>
                        <rect x="210" y="190" width="148" height="8" rx="4" fill="#FFFFFF" opacity=".16"></rect>
                        <rect x="210" y="190" width="104" height="8" rx="4" fill="#8091FF"></rect>
                        <circle cx="360" cy="204" r="12" fill="#6478F5"></circle>
                        <circle cx="336" cy="204" r="12" fill="#FFFFFF" opacity=".72"></circle>
                        <circle cx="312" cy="204" r="12" fill="#C7D0FF"></circle>
                    </svg>
                </div>

                <div class="teamo-auth-showcase-footer">© {{ date('Y') }} Teamo</div>
            </aside>

            <main class="col-12 col-lg-6 d-flex flex-column align-items-center justify-content-center teamo-auth-panel">
                <div class="teamo-auth-form">
                    <div class="d-flex d-lg-none justify-content-center mb-4">
                        <a href="{{ url('/') }}" class="d-inline-flex" aria-label="Teamo">
                            <img src="{{ asset('assets/brand/teamo-logo-light.svg') }}" class="teamo-auth-mobile-logo teamo-auth-mobile-logo-light" alt="Teamo">
                            <img src="{{ asset('assets/brand/teamo-logo-dark.svg') }}" class="teamo-auth-mobile-logo teamo-auth-mobile-logo-dark" alt="" aria-hidden="true">
                        </a>
                    </div>

                    <div class="card teamo-auth-card border-0 shadow-sm">
                        <div class="card-body p-4 p-sm-5">
                            @yield('content')
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary teamo-theme-toggle" data-teamo-theme-toggle
                            aria-label="Activer le mode sombre" title="Activer le mode sombre" aria-pressed="false">
                            <i class="bi bi-moon-stars" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="{{ asset('js/teamo.js') }}"></script>
    @stack('scripts')
</body>
</html>
