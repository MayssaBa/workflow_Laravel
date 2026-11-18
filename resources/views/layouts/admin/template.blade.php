<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Projects Manager 360')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; background: #f4f7fb; color: #172033; }

        .admin-sidebar {
            --bs-offcanvas-width: 272px;
            background: linear-gradient(165deg, #111827 0%, #172338 100%);
            color: #cbd5e1;
            border-right: 1px solid rgba(255, 255, 255, .06);
        }
        .admin-brand { color: #fff; font-size: 1.05rem; font-weight: 700; }
        .admin-brand:hover { color: #fff; }
        .admin-brand-mark {
            display: grid; width: 42px; height: 42px; place-items: center;
            border-radius: 14px; color: #fff; font-size: 1.2rem;
            background: linear-gradient(145deg, #2563eb, #4f46e5);
        }
        .admin-brand-caption, .admin-nav-caption {
            color: #8795aa; font-size: .64rem; font-weight: 700; letter-spacing: .12em;
        }
        .admin-sidebar-header {
            min-height: 84px; padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }
        .admin-nav-link {
            display: flex; align-items: center; gap: .8rem; min-height: 46px;
            padding: .6rem .85rem; border: 1px solid transparent; border-radius: .85rem;
            color: #aebbd0; text-decoration: none; font-size: .94rem; font-weight: 500;
            transition: background-color .2s ease, color .2s ease, transform .2s ease;
        }
        .admin-nav-link:hover { color: #fff; background: rgba(255, 255, 255, .06); transform: translateX(2px); }
        .admin-nav-link.active {
            color: #fff; border-color: rgba(96, 165, 250, .17);
            background: linear-gradient(100deg, rgba(37, 99, 235, .26), rgba(79, 70, 229, .12));
            box-shadow: inset 3px 0 #60a5fa;
        }
        .admin-nav-icon {
            display: grid; width: 34px; height: 34px; place-items: center;
            border-radius: 10px; background: rgba(255, 255, 255, .055); font-size: 1.05rem;
        }
        .admin-nav-link.active .admin-nav-icon { background: rgba(96, 165, 250, .16); color: #93c5fd; }
        .admin-sidebar-footer {
            display: flex; align-items: center; gap: .75rem; padding: .8rem;
            border: 1px solid rgba(255, 255, 255, .08); border-radius: .9rem;
            background: rgba(255, 255, 255, .045); font-size: .85rem;
        }
        .admin-avatar {
            display: grid; width: 38px; height: 38px; flex: 0 0 auto; place-items: center;
            border-radius: 50%; background: #2563eb; color: #fff; font-weight: 700;
        }
        .admin-menu-toggle {
            z-index: 1040; width: 44px; height: 44px; border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, .12); background: #172338; color: #fff;
        }
        .admin-menu-toggle:hover, .admin-menu-toggle:focus-visible { background: #253248; color: #fff; }
        .admin-main { min-width: 0; }
        .admin-content { max-width: 1440px; }

        @media (max-width: 991.98px) { .admin-main { padding-top: 4rem; } }
        @media (min-width: 992px) {
            .admin-sidebar { position: fixed; inset: 0 auto 0 0; z-index: 1030; height: 100vh; }
            .admin-main { margin-left: 272px; }
        }
    </style>
    @stack('styles')
</head>
<body>

    @include('layouts.admin.navbar')

    <main class="admin-main">
        <div class="container-fluid admin-content px-3 px-lg-4 py-4">
            @if (session('success'))
                <x-alert>{{ session('success') }}</x-alert>
            @endif

            @if (session('error'))
                <x-alert type="danger">{{ session('error') }}</x-alert>
            @endif

            @if ($errors->any())
                <x-alert type="danger">
                    <p class="fw-semibold mb-1">Le formulaire contient des erreurs :</p>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
