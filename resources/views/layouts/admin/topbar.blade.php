@php
    $user = auth()->user();
    $role = $user->isAdmin() ? 'admin' : ($user->isManager() ? 'manager' : 'employee');
    $roleLabel = ['admin' => 'ADMIN', 'manager' => 'MANAGER', 'employee' => 'EMPLOYEE'][$role];
    $badgeClass = ['admin' => 'text-bg-primary', 'manager' => 'text-bg-info', 'employee' => 'text-bg-secondary'][$role];
@endphp

<header class="admin-topbar sticky-top">
    <div class="container-fluid d-flex align-items-center gap-3 px-3 px-lg-4">
        <button class="btn admin-topbar-menu d-lg-none" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Ouvrir le menu">
            <i class="bi bi-list" aria-hidden="true"></i>
        </button>

        <h1 class="admin-topbar-title h6 mb-0 text-truncate">
            @yield('title', 'Tableau de bord')
        </h1>

        <div class="ms-auto d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm admin-topbar-action teamo-theme-toggle" data-teamo-theme-toggle
                aria-label="Activer le mode sombre" title="Activer le mode sombre" aria-pressed="false">
                <i class="bi bi-moon-stars" aria-hidden="true"></i>
            </button>

            <span class="btn btn-sm admin-topbar-action disabled position-relative" aria-disabled="true"
                title="bientôt disponible" aria-label="Notifications : bientôt disponible">
                <i class="bi bi-bell" aria-hidden="true"></i>
            </span>

            <div class="dropdown">
                <button class="btn btn-sm admin-topbar-user dropdown-toggle d-flex align-items-center gap-2"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu de {{ $user->name }}">
                    <x-ui.avatar :user="$user" />
                    <span class="d-none d-lg-block text-start small">
                        <span class="d-block fw-semibold text-truncate">{{ $user->name }}</span>
                        <span class="badge {{ $badgeClass }}">{{ $roleLabel }}</span>
                    </span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">Mon profil</a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button class="dropdown-item" type="submit">Déconnexion</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
