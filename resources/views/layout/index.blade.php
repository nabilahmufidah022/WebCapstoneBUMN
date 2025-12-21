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
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css" >
  </head>
  <body>
    <nav class="navbar">
      <div class="logo_item">
        <i class="bx bx-menu" id="sidebarOpen"></i>
        <img src="{{ asset('img/rumah-bumn.png') }}" alt=""> Rumah BUMN
      </div>

      <div class="navbar_content">
        <i class="bi bi-grid"></i>
        <i class='bx bx-sun' id="darkLight"></i>
        <i class='bx bx-bell'></i>
        @auth
        <div class="dropdown">
          <img src="{{ Auth::user()->profile_image ? asset('img/' . Auth::user()->profile_image) : asset('img/profile.jpg') }}" alt="Profile" class="profile dropdown-toggle" id="profileToggle" data-bs-toggle="dropdown" aria-expanded="false" />
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
            <li><a class="dropdown-item" href="{{ route('settings') }}">Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">Logout</button>
              </form>
            </li>
          </ul>
        </div>
        @endauth
      </div>
    </nav>

    <<nav class="sidebar">
      <div class="menu_content">
        <ul class="menu_items">
          <li class="item">
            <a href="{{ route('dashboard') }}" class="nav_link">
              <span class="navlink_icon">
                <i class="bx bx-home-alt"></i>
              </span>
              <span class="navlink">Dashboard Laporan</span>
            </a>
          </li>

          <li class="item">
            <div class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="bx bxs-group"></i>
              </span>
              <span class="navlink">Partnership</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <li><a href="{{ route('list_mitra') }}" class="nav_link sublink">Daftar Mitra</a></li>
              
              @if(Auth::user()->usertype === 'admin')
              <li><a href="{{ route('kelola_pendaftaran') }}" class="nav_link sublink">Kelola Pendaftaran</a></li>
              @endif

              <li><a href="{{ route('mitra.participation.index') }}" class="nav_link sublink">Keikutsertaan Mitra</a></li>
            </ul>
          </li>

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
        </ul>

        <div class="bottom_content">
          <div class="bottom expand_sidebar">
            <span> Expand</span>
            <i class='bx bx-log-in' ></i>
          </div>
          <div class="bottom collapse_sidebar">
            <span> Collapse</span>
            <i class='bx bx-log-out'></i>
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