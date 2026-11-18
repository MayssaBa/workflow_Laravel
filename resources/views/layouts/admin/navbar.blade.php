@php
    $user = auth()->user();
    $role = $user->isAdmin() ? 'admin' : ($user->isManager() ? 'manager' : 'employee');
    $roleLabel = ['admin' => 'Administrateur', 'manager' => 'Manager', 'employee' => 'Employé'][$role];

    // [nom de route, libellé, motif routeIs, icône Bootstrap Icons]
    // Un lien n'apparaît que si sa route existe : ajoute tes routes ici au fil des étapes.
    $menus = [
        'admin' => [
            ['admin.dashboard', 'Tableau de bord', 'admin.dashboard', 'bi-speedometer2'],
            ['admin.employee.index', 'Employés', 'admin.employee.*', 'bi-people'],
            ['admin.department.index', 'Départements', 'admin.department.*', 'bi-diagram-3'],
            ['admin.team.index', 'Équipes', 'admin.team.*', 'bi-person-workspace'],
            ['request.index', 'Demandes', 'request.*', 'bi-chat-left-text'],
            ['profile.edit', 'Mon profil', 'profile.*', 'bi-person-circle'],
        ],
        'manager' => [
            ['manager.dashboard', 'Tableau de bord', 'manager.dashboard', 'bi-speedometer2'],
            ['manager.project.index', 'Projets', 'manager.project.*', 'bi-folder2-open'],
            ['manager.task.index', 'Tâches', 'manager.task.*', 'bi-check2-square'],
            ['request.index', 'Demandes', 'request.*', 'bi-chat-left-text'],
            ['profile.edit', 'Mon profil', 'profile.*', 'bi-person-circle'],
        ],
        'employee' => [
            ['employee.dashboard', 'Tableau de bord', 'employee.dashboard', 'bi-speedometer2'],
            ['employee.task.index', 'Mes tâches', 'employee.task.*', 'bi-check2-square'],
            ['request.index', 'Mes demandes', 'request.*', 'bi-chat-left-text'],
            ['profile.edit', 'Mon profil', 'profile.*', 'bi-person-circle'],
        ],
    ];

    $homeRoute = $role . '.dashboard';

    $unread = 0;
    try {
        $unread = $user->unreadNotifications()->count();
    } catch (\Throwable $e) {
        $unread = 0;
    }
@endphp

<button class="admin-menu-toggle btn d-lg-none position-fixed top-0 start-0 m-3 shadow" type="button"
    data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Ouvrir le menu">
    <i class="bi bi-list fs-4" aria-hidden="true"></i>
</button>

<aside class="admin-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
    <div class="admin-sidebar-header d-flex align-items-center justify-content-between">
        <a class="admin-brand text-decoration-none d-flex align-items-center gap-3"
            href="{{ Route::has($homeRoute) ? route($homeRoute) : url('/') }}" id="adminSidebarLabel">
            <span class="admin-brand-mark" aria-hidden="true"><i class="bi bi-kanban"></i></span>
            <span class="d-flex flex-column">
                <span>Projects Manager 360</span>
                <span class="admin-brand-caption">{{ $roleLabel }}</span>
            </span>
        </a>
        <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Fermer"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column p-3">
        <div class="admin-nav-caption px-3 mb-2">MENU</div>
        <nav class="nav flex-column gap-2" aria-label="Navigation principale">
            @foreach ($menus[$role] as [$name, $label, $pattern, $icon])
                @if (Route::has($name))
                    <a class="admin-nav-link @if (request()->routeIs($pattern)) active @endif"
                        href="{{ route($name) }}" @if (request()->routeIs($pattern)) aria-current="page" @endif>
                        <span class="admin-nav-icon" aria-hidden="true"><i class="bi {{ $icon }}"></i></span>
                        <span>{{ $label }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="admin-sidebar-footer mt-auto">
            <a class="d-flex align-items-center gap-2 flex-grow-1 overflow-hidden text-decoration-none"
                href="{{ Route::has('profile.edit') ? route('profile.edit') : '#' }}" title="Mon profil">
                <span class="admin-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                <span class="text-truncate">
                    <span class="d-block text-white fw-semibold text-truncate">{{ $user->name }}</span>
                    <span class="d-block small" style="color:#8795aa">{{ $roleLabel }}</span>
                </span>
            </a>
            <a class="btn btn-sm btn-outline-light position-relative"
                href="{{ Route::has('notification.index') ? route('notification.index') : '#' }}"
                title="Notifications" aria-label="Notifications ({{ $unread }} non lues)">
                <i class="bi bi-bell" aria-hidden="true"></i>
                @if ($unread > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger">{{ $unread }}</span>
                @endif
            </a>
            @if (Route::has('logout'))
                <form method="post" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="btn btn-sm btn-outline-light" type="submit" title="Se déconnecter" aria-label="Se déconnecter">
                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>
</aside>
