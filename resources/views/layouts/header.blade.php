<header class="navbar navbar-expand-md d-print-none" >
    <div class="container-xl">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
        <a href=".">
          {{-- <img src="./static/logo.svg" width="110" height="32" alt="Tabler" class="navbar-brand-image"> --}}
          <i class="fab fa-apple fa-lg"></i>
        </a>
      </h1>
      <div class="navbar-nav flex-row order-md-last">
        <div class="d-none d-md-flex">
          <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Enable dark mode" data-bs-toggle="tooltip"
       data-bs-placement="bottom">
            <!-- Download SVG icon from http://tabler-icons.io/i/moon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" /></svg>
          </a>
          <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Enable light mode" data-bs-toggle="tooltip"
       data-bs-placement="bottom">
            <!-- Download SVG icon from http://tabler-icons.io/i/sun -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" /></svg>
          </a>
          
        </div>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
            @auth
              <span class="avatar avatar-sm" style="background-image: url('{{ asset(Auth::user()->avatar ? 'storage/' . Auth::user()->avatar : 'storage/avatar/default-avatar.png') }}')"></span>
            @endauth


            <div class="d-none d-xl-block ps-2">
              <div>{{auth()->user()->name}}</div>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
            <a href="./profile.html" class="dropdown-item">Profile</a>
            <div class="dropdown-divider"></div>
              <a href="{{route('password.change')}}" class="dropdown-item">Settings</a>
              {{-- <a href="./sign-in.html" class="dropdown-item">Logout</a> --}}
              <form action="{{route('logout')}}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item">Logout</button>
              </form>
          </div>
        </div>
      </div>
      @role('admin')
      <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="./" >
                <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
                </span>
                <span class="nav-link-title">
                  Home
                </span>
              </a>
            </li>
            
            <li class="nav-item">
              <a class="nav-link" href="./form-elements.html" >
                <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/checkbox -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l3 3l8 -8" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                </span>
                <span class="nav-link-title">
                  Forms
                </span>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Murojaah</span>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item {{request()->routeIs('murojaahs')?'active':''}}" href="{{route('murojaahs')}}">
                  Data
                </a>
                <a class="dropdown-item {{request()->routeIs('murojaahs.create')?'active':''}}" href="{{route('murojaahs.create')}}">
                  Create
                </a>
                <a class="dropdown-item {{request()->routeIs('murojaahs.rekap')?'active':''}}" href="{{route('murojaahs.rekap')}}">
                  Rekap
                </a>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Absen</span>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item {{request()->routeIs('absensisantris.index')?'active':''}}" href="{{route('absensisantris.index')}}">
                  Data Absen Santri
                </a>
                <a class="dropdown-item {{request()->routeIs('absensisantris.create')?'active':''}}" href="{{route('absensisantris.create')}}">
                  Create Absen Santri
                </a>
                <a class="dropdown-item {{request()->routeIs('absensisantris.rekapabsen')?'active':''}}" href="{{route('absensisantris.rekapabsen')}}">
                  Rekap Absen Santri
                </a>
                <a class="dropdown-item {{request()->routeIs('absensiustadzs.index')?'active':''}}" href="{{route('absensiustadzs.index')}}">
                  Data Absen Ustadz
                </a>
              
                
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Master</span>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item {{request()->routeIs('kelasnyas')?'active':''}}" href="{{route('kelasnyas')}}">
                  Kelas
                </a>
                <a class="dropdown-item {{request()->routeIs('kelompoks')?'active':''}}" href="{{route('kelompoks')}}">
                  Kelompok
                </a>
                <a class="dropdown-item {{request()->routeIs('ustadzs')?'active':''}}" href="{{route('ustadzs')}}">
                  Ustadz
                </a>
                <a class="dropdown-item {{request()->routeIs('santris')?'active':''}}" href="{{route('santris')}}">
                  Santri
                </a>
                <a class="dropdown-item {{request()->routeIs('hariliburs')?'active':''}}" href="{{route('hariliburs')}}">
                  Hari Libur
                </a>
              </div>
            </li>
          </ul>
        </div>
      </div>
      @endrole
      @role('ustadz')
      <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="{{route('ustadz.dashboard')}}" >
                <span class="nav-link-icon d-md-none d-lg-inline-block"> 
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">
                  Home
                </span>
              </a>
            </li>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Murojaah</span>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item {{request()->routeIs('murojaahs')?'active':''}}" href="{{route('murojaahs')}}">
                  Data
                </a>
                <a class="dropdown-item {{request()->routeIs('murojaahs.create')?'active':''}}" href="{{route('murojaahs.create')}}">
                  Create
                </a>
                
                
              </div>
            </li>
           
            <li class="nav-item">
              <a class="nav-link" href="{{route('absensiustadzs.rekapabsenustadz')}}" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">
                  Rekap Absen
                </span>
              </a>
            </li>

          </ul>
        </div>
      </div>
      @endrole

      @role('santri')
      <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="{{route('santri.dashboard')}}" >
                <span class="nav-link-icon d-md-none d-lg-inline-block"> 
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">
                  Home
                </span>
              </a>
            </li>
            
            <li class="nav-item">
              <a class="nav-link" href="{{route('absensisantris.rekapabsensantri')}}" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">
                  Rekap Absen
                </span>
              </a>
            </li>
           
          </ul>
        </div>
      </div>
      @endrole
    </div>
  </header>