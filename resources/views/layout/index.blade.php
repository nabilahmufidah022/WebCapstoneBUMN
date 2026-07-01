<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Rumah BUMN - Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">
    
    {{-- Styling Tambahan khusus untuk kelenturan layout soft badge notifikasi --}}
    <style>
        .bg-primary-soft { background-color: #e8f0fe !important; color: #1a73e8 !important; }
        .bg-info-soft { background-color: #e2f0d9 !important; color: #38761d !important; }
        .bg-danger-soft { background-color: #fce8e6 !important; color: #c5221f !important; }
        .bg-warning-soft { background-color: #fef7e0 !important; color: #b06000 !important; }
        .bg-success-soft { background-color: #e6f4ea !important; color: #137333 !important; }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="logo_item">
            <i class="bx bx-menu" id="sidebarOpen"></i>
            <img src="{{ asset('img/rumah-bumn.png') }}" alt="">
            Rumah BUMN
        </div>

        <div class="navbar_content">

            {{-- ==========================================================================
                 🌟 INTEGRASI FITUR: PUSAT NOTIFIKASI DROPDOWN (SERVER MITRA & ADMIN)
                 ========================================================================== --}}
            @auth
                <div class="dropdown me-2">
                    <button class="btn btn-link position-relative text-dark p-1 border-0" type="button" id="dropdownNotification" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none;">
                        <i class="bx bx-bell fs-4" style="vertical-align: middle;"></i>
                        {{-- Logika hitung data notifikasi yang belum dibaca --}}
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger" style="padding: 4px; margin-top: 4px; margin-left: -4px;">
                                <span class="visually-hidden">Notifikasi Baru</span>
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-2 mt-2 rounded-4" aria-labelledby="dropdownNotification" style="width: 340px; max-height: 420px; overflow-y: auto;">
                        <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top-4">
                            <span class="fw-bold text-dark small"><i class="bx bx-notification me-1 text-primary"></i>Pusat Notifikasi</span>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <a href="{{ route('notifications.markAllRead') }}" class="text-primary text-decoration-none fw-bold" style="font-size: 11px;">Tandai Dibaca</a>
                            @endif
                        </div>
                        
                        @forelse(Auth::user()->notifications->take(10) as $notification)
                            <li class="px-3 py-2.5 border-bottom dropdown-item" style="white-space: normal; background-color: {{ $notification->read_at ? 'transparent' : '#f4f7fe' }};">
                                <div class="d-flex gap-2.5 align-items-start">
                                    <div class="badge bg-{{ $notification->data['type'] ?? 'primary' }}-soft p-2 rounded-circle">
                                        <i class="bx {{ $notification->data['icon'] ?? 'bx-bell' }} fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        {{-- Mendukung pembacaan array data Laravel maupun record kolom tabel lama --}}
                                        <div class="small fw-bold text-dark mb-0.5">
                                            {{ $notification->data['title'] ?? $notification->title }}
                                        </div>
                                        <div class="text-muted" style="font-size: 11px; line-height: 1.4;">
                                            {{ $notification->data['message'] ?? $notification->message }}
                                        </div>
                                        <small class="text-muted d-block mt-1" style="font-size: 9px;">
                                            <i class="bx bx-time-five me-0.5"></i>{{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <div class="text-center 本 text-muted py-4">
                                <i class="bx bx-bell-off fs-2 d-block mb-1 opacity-50"></i>
                                <span class="small d-block">Tidak ada notifikasi baru masuk.</span>
                            </div>
                        @endforelse
                    </ul>
                </div>
            @endauth
            {{-- ========================================================================== --}}

            @auth
                <div class="dropdown">
                    <img
                        src="{{ Auth::user()->profile_image ? asset('img/' . Auth::user()->profile_image) : asset('img/profile.jpg') }}"
                        alt="Profile"
                        class="profile dropdown-toggle"
                        id="profileToggle"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    />

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile') }}">
                                Profile
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="{{ route('settings') }}">
                                Settings
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </nav>

    <nav class="sidebar">
        <div class="menu_content">
            <ul class="menu_items">

                {{-- DASHBOARD (Akses Semua User) --}}
                <li class="item">
                    <a href="{{ route('dashboard') }}" class="nav_link">
                        <span class="navlink_icon">
                            <i class="bx bx-home-alt"></i>
                        </span>
                        <span class="navlink">Dashboard Monitoring</span>
                    </a>
                </li>

                {{-- MENU PARTNERSHIP (HANYA UNTUK ADMIN) --}}
                @if(Auth::user()->usertype === 'admin')
                    <li class="item">
                        <div class="nav_link submenu_item">
                            <span class="navlink_icon">
                                <i class="bx bxs-group"></i>
                            </span>
                            <span class="navlink">Partnership</span>
                            <i class="bx bx-chevron-right arrow-left"></i>
                        </div>

                        <ul class="menu_items submenu">
                            <li>
                                <a href="{{ route('mitra.participation.index') }}" class="nav_link sublink">
                                    Silabus Pelatihan
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('list_mitra') }}" class="nav_link sublink">
                                    Pusat Data Mitra
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('kelola_pendaftaran') }}" class="nav_link sublink">
                                    Verifikasi Pendaftaran
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- MENU ACCOUNT MANAGEMENT (Hanya Admin) --}}
                @if(Auth::user()->usertype === 'admin')
                    <li class="item">
                        <a href="{{ route('account') }}" class="nav_link">
                            <span class="navlink_icon">
                                <i class="bx bxs-user-detail"></i>
                            </span>
                            <span class="navlink">Account Management</span>
                        </a>
                    </li>
                @endif

                {{-- MENU PENGELOLAAN PROFIL (Hanya Mitra) --}}
                @if(Auth::user()->usertype === 'mitra')
                    <li class="item">
                        <a href="{{ route('profile') }}" class="nav_link">
                            <span class="navlink_icon">
                                <i class="bx bxs-user-rectangle"></i>
                            </span>
                            <span class="navlink">Pengelolaan Profil</span>
                        </a>
                    </li>
                @endif

            </ul>

            <div class="bottom_content">
                <div class="bottom expand_sidebar">
                    <span>Expand</span>
                    <i class="bx bx-log-in"></i>
                </div>

                <div class="bottom collapse_sidebar">
                    <span>Collapse</span>
                    <i class="bx bx-log-out"></i>
                </div>
            </div>
        </div>
    </nav>

    <main class="main">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>

</body>
</html>