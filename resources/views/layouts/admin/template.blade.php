<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') · Teamo</title>
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
<body>

    @include('layouts.admin.navbar')
    @include('layouts.admin.topbar')

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
    <script src="{{ asset('js/teamo.js') }}"></script>
    @stack('scripts')
</body>
</html>
