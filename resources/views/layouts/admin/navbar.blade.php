@php
    $user = auth()->user();
    $role = $user->isAdmin() ? 'admin' : ($user->isManager() ? 'manager' : 'employee');
    $roleLabel = ['admin' => 'ADMIN', 'manager' => 'MANAGER', 'employee' => 'EMPLOYEE'][$role];
    $badgeClass = ['admin' => 'text-bg-primary', 'manager' => 'text-bg-info', 'employee' => 'text-bg-secondary'][$role];
    $homeRoute = $role . '.dashboard';

    $menus = [
        'admin' => [
            ['admin.dashboard', 'Tableau de bord', 'admin.dashboard', 'bi-speedometer2'],
            ['projects.index', 'Projets', 'projects.*', 'bi-folder2-open'],
            ['admin.employees.index', 'Employés', 'admin.employees.*', 'bi-people'],
            ['admin.department.index', 'Départements', 'admin.department.*', 'bi-diagram-3'],
            ['admin.team.index', 'Équipes', 'admin.team.*', 'bi-person-workspace'],
            ['request.index', 'Demandes', 'request.*', 'bi-chat-left-text'],
        ],
        'manager' => [
            ['manager.dashboard', 'Tableau de bord', 'manager.dashboard', 'bi-speedometer2'],
            ['projects.index', 'Projets', 'projects.*', 'bi-folder2-open'],
            ['tasks.index', 'Tâches', 'tasks.*', 'bi-check2-square'],
            ['request.index', 'Demandes', 'request.*', 'bi-chat-left-text'],
        ],
        'employee' => [
            ['employee.dashboard', 'Tableau de bord', 'employee.dashboard', 'bi-speedometer2'],
            ['projects.index', 'Projets', 'projects.*', 'bi-folder2-open'],
            ['tasks.index', 'Mes tâches', 'tasks.*', 'bi-check2-square'],
            ['request.index', 'Mes demandes', 'request.*', 'bi-chat-left-text'],
        ],
    ];
@endphp

<aside class="admin-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
    <div class="admin-sidebar-header d-flex align-items-center justify-content-between">
        <a class="admin-brand text-decoration-none d-flex align-items-center gap-2"
            href="{{ Route::has($homeRoute) ? route($homeRoute) : url('/') }}" id="adminSidebarLabel">
            <img class="admin-brand-logo" src="{{ asset('assets/brand/teamo-logo-dark.svg') }}" alt="Teamo">
        </a>
        <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas"
            data-bs-target="#adminSidebar" aria-label="Fermer"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column p-3">
        @if (Route::has('profile.edit'))
            <a class="admin-profile-card d-flex align-items-center gap-2 text-decoration-none mb-3"
                href="{{ route('profile.edit') }}" title="Mon profil">
                <x-ui.avatar :user="$user" />
                <span class="min-w-0">
                    <span class="d-block text-white fw-semibold text-truncate small">{{ $user->name }}</span>
                    <span class="badge {{ $badgeClass }}">{{ $roleLabel }}</span>
                </span>
            </a>
        @endif

        <div class="admin-nav-caption px-2 mb-1">Menu</div>
        <nav class="admin-nav nav flex-column" aria-label="Navigation principale">
            @foreach ($menus[$role] as [$name, $label, $pattern, $icon])
                @if (Route::has($name))
                    <a class="admin-nav-link @if (request()->routeIs($pattern)) active @endif"
                        href="{{ route($name) }}" @if (request()->routeIs($pattern)) aria-current="page" @endif>
                        <span class="admin-nav-icon" aria-hidden="true"><i class="bi {{ $icon }}"></i></span>
                        <span>{{ $label }}</span>
                    </a>
                @else
                    <span class="admin-nav-link disabled" aria-disabled="true" title="bientôt disponible">
                        <span class="admin-nav-icon" aria-hidden="true"><i class="bi {{ $icon }}"></i></span>
                        <span>{{ $label }}</span>
                    </span>
                @endif
            @endforeach
        </nav>
    </div>
</aside>
