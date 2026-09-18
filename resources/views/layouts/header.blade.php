@inject('roleTaskService', 'App\Services\RoleTaskService')

@php
    $roleTask = $roleTaskService->getForUser();
@endphp

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


{{-- TUGAS SAYA --}}
<div class="nav-item dropdown ms-2">

    <a href="#"
       class="nav-link px-0"
       data-bs-toggle="dropdown"
       aria-label="Tugas saya"
       title="Tugas Saya"
       data-bs-toggle="tooltip"
       data-bs-placement="bottom">

        {{-- sementara gunakan SVG/icon sederhana --}}
        <svg xmlns="http://www.w3.org/2000/svg"
             class="icon"
             width="24"
             height="24"
             viewBox="0 0 24 24"
             stroke-width="2"
             stroke="currentColor"
             fill="none"
             stroke-linecap="round"
             stroke-linejoin="round">

            <path stroke="none"
                  d="M0 0h24v24H0z"
                  fill="none"/>

            <path d="M10 5h-6a2 2 0 0 0 -2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2 -2v-6"/>

            <path d="M14 3h7v7"/>

            <path d="M10 14l11 -11"/>
        </svg>

    </a>


    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card"
         style="width: 380px;">

        <div class="card">

            {{-- HEADER --}}
            <div class="card-header">

                <div class="d-flex align-items-center">

                    <span class="avatar avatar-sm bg-{{ $roleTask['color'] }}-lt me-3">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="24"
                             height="24"
                             viewBox="0 0 24 24"
                             stroke-width="2"
                             stroke="currentColor"
                             fill="none"
                             stroke-linecap="round"
                             stroke-linejoin="round">

                            <path stroke="none"
                                  d="M0 0h24v24H0z"
                                  fill="none"/>

                            <path d="M12 3l8 4v5c0 5 -3.5 8 -8 9c-4.5 -1 -8 -4 -8 -9v-5l8 -4"/>
                            <path d="M9 12l2 2l4 -4"/>

                        </svg>

                    </span>

                    <div>

                        <h3 class="card-title mb-1">
                            {{ $roleTask['title'] }}
                        </h3>

                        <div class="text-secondary small">
                            {{ $roleTask['description'] }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- TASKS --}}

            <div class="card-body">

                <div class="text-uppercase text-secondary fw-bold small mb-3">
                    Yang Perlu Anda Kerjakan
                </div>


                @if(count($roleTask['tasks']) > 0)

                    <div class="list-group list-group-flush">

                        @foreach($roleTask['tasks'] as $task)

                            <a href="{{ $task['url'] }}"
                              class="list-group-item list-group-item-action px-0">

                                <div class="row align-items-center">

                                    {{-- ICON --}}
                                    <div class="col-auto">

                                        <span class="avatar avatar-sm bg-{{ $roleTask['color'] }}-lt">

                                            @if($task['icon'] === 'file-text')

                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    width="20"
                                                    height="20"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="2"
                                                    stroke="currentColor"
                                                    fill="none"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round">

                                                    <path stroke="none"
                                                          d="M0 0h24v24H0z"
                                                          fill="none"/>

                                                    <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2"/>
                                                    <path d="M9 17h6"/>
                                                    <path d="M9 13h6"/>
                                                </svg>

                                            @else

                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    width="20"
                                                    height="20"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="2"
                                                    stroke="currentColor"
                                                    fill="none"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round">

                                                    <path stroke="none"
                                                          d="M0 0h24v24H0z"
                                                          fill="none"/>

                                                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                                                    <path d="M9 5a3 3 0 0 1 6 0"/>
                                                    <path d="M9 12h6"/>
                                                    <path d="M9 16h6"/>
                                                </svg>

                                            @endif

                                        </span>

                                    </div>


                                    {{-- TITLE --}}
                                    <div class="col">

                                        <div class="text-body small fw-medium">
                                            {{ $task['title'] }}
                                        </div>

                                        <div class="text-secondary small">
                                            Perlu ditindaklanjuti
                                        </div>

                                    </div>


                                    {{-- COUNT --}}
                                    <div class="col-auto">

                                        <span class="badge bg-red text-white">
                                            {{ $task['count'] }}
                                        </span>

                                    </div>

                                </div>

                            </a>

                        @endforeach

                    </div>

                @else

                    <div class="text-center py-3">

                        <span class="avatar avatar-lg bg-success-lt mb-2">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                fill="none"
                                stroke-linecap="round"
                                stroke-linejoin="round">

                                <path stroke="none"
                                      d="M0 0h24v24H0z"
                                      fill="none"/>

                                <path d="M5 12l5 5l10 -10"/>

                            </svg>

                        </span>

                        <div class="text-secondary small">
                            Tidak ada pekerjaan yang perlu ditindaklanjuti.
                        </div>

                    </div>

                @endif

            </div>



            {{-- WORKFLOW --}}
            <div class="card-footer">

                <div class="text-uppercase text-secondary fw-bold small mb-2">
                    Alur Pekerjaan
                </div>

                <div class="text-secondary small">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="16"
                         height="16"
                         viewBox="0 0 24 24"
                         stroke-width="2"
                         stroke="currentColor"
                         fill="none"
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         class="me-1">

                        <path stroke="none"
                              d="M0 0h24v24H0z"
                              fill="none"/>

                        <path d="M5 12l14 0"/>
                        <path d="M13 18l6 -6"/>
                        <path d="M13 6l6 6"/>

                    </svg>

                    {{ $roleTask['flow'] }}

                </div>

            </div>

        </div>

    </div>

</div>




        <div class="nav-item dropdown">
          <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
            @auth
              <span class="avatar avatar-sm" style="background-image: url('{{ asset(Auth::user()->avatar ? 'storage/' . Auth::user()->avatar : 'storage/avatars/default-avatar.png') }}')"></span>
            @endauth


            <div class="d-none d-xl-block ps-2">
              <div>{{auth()->user()->name}}</div>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
            <a href="{{route('profile.index')}}" class="dropdown-item">Profile</a>
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

      @role('superadmin')
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
            <li class="nav-item">
              <a class="nav-link" href="{{ route('operasional.index') }}" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Operasional</span>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Master</span>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item {{request()->routeIs('cabangs')?'active':''}}" href="{{route('cabangs')}}">
                  Cabang
                </a>
                <a class="dropdown-item {{request()->routeIs('karyawans')?'active':''}}" href="{{route('karyawans')}}">
                  Karyawan
                </a>
              </div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Report</span>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item {{request()->routeIs('reports.pembiayaan')?'active':''}}" href="{{route('reports.pembiayaan')}}">
                  Report Pembiayaan
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.pencairan')?'active':''}}" href="{{route('reports.pencairan')}}">
                  Report Pencairan
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.angsuran')?'active':''}}" href="{{route('reports.angsuran')}}">
                  Report Angsuran
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.pelunasan')?'active':''}}" href="{{route('reports.pelunasan')}}">
                  Report Pelunasan
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.outstanding')?'active':''}}" href="{{route('reports.outstanding')}}">
                  Report Outstanding
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.jatuhTempo')?'active':''}}" href="{{route('reports.jatuhTempo')}}">
                  Report Jatuh Tempo
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.npl')?'active':''}}" href="{{route('reports.npl')}}">
                  Report NPL
                </a>
              </div>
            </li>
          </ul>
        </div>
      </div>
      @endrole
      @role('spvmarketing|marketing|admincabang')
      <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
          <ul class="navbar-nav">
            <li class="nav-item">
                @if(auth()->user()->hasRole('spvmarketing'))
                    <a class="nav-link" href="{{ route('spvmarketing.dashboard') }}">

                @elseif(auth()->user()->hasRole('marketing'))
                    <a class="nav-link" href="{{ route('marketing.dashboard') }}">

                @elseif(auth()->user()->hasRole('admincabang'))
                    <a class="nav-link" href="{{ route('admincabang.dashboard') }}">
                @endif
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="fa-solid fa-house"></i>
                </span>
                <span class="nav-link-title">
                  Home
                </span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('pengajuan.create') }}" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Pengajuan</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('pembiayaan.index') }}" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Pembiayaan</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('angsuran.index') }}" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Angsuran</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('pelunasan.pengajuan.diskon') }}">
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                      <i class="fa-solid fa-file-invoice-dollar"></i>
                  </span>
                  <span class="nav-link-title">
                      Pengajuan Diskon
                  </span>
              </a>
          </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('operasional.index') }}" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Operasional</span>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Report</span>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item {{request()->routeIs('reports.pembiayaan')?'active':''}}" href="{{route('reports.pembiayaan')}}">
                  Report Pembiayaan
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.pencairan')?'active':''}}" href="{{route('reports.pencairan')}}">
                  Report Pencairan
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.angsuran')?'active':''}}" href="{{route('reports.angsuran')}}">
                  Report Angsuran
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.pelunasan')?'active':''}}" href="{{route('reports.pelunasan')}}">
                  Report Pelunasan
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.outstanding')?'active':''}}" href="{{route('reports.outstanding')}}">
                  Report Outstanding
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.jatuhTempo')?'active':''}}" href="{{route('reports.jatuhTempo')}}">
                  Report Jatuh Tempo
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.npl')?'active':''}}" href="{{route('reports.npl')}}">
                  Report NPL
                </a>
              </div>
            </li>
          </ul>
        </div>
      </div>
      @endrole
     
      @role('komisaris|direktur|kacab')
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
              <a class="nav-link" href="{{ route('pimpinan.index') }}" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Pengajuan</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('diskon-denda.approval') }}" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Diskon Denda</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('pelunasan.persetujuan.diskon') }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <i class="fa-solid fa-file-signature"></i>
                </span>
                <span class="nav-link-title">
                    Persetujuan Diskon
                </span>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#navbar-help" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="nav-icon fas fa-cog"></i>
                </span>
                <span class="nav-link-title">Report</span>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item {{request()->routeIs('reports.pembiayaan')?'active':''}}" href="{{route('reports.pembiayaan')}}">
                  Report Pembiayaan
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.pencairan')?'active':''}}" href="{{route('reports.pencairan')}}">
                  Report Pencairan
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.angsuran')?'active':''}}" href="{{route('reports.angsuran')}}">
                  Report Angsuran
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.pelunasan')?'active':''}}" href="{{route('reports.pelunasan')}}">
                  Report Pelunasan
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.outstanding')?'active':''}}" href="{{route('reports.outstanding')}}">
                  Report Outstanding
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.jatuhTempo')?'active':''}}" href="{{route('reports.jatuhTempo')}}">
                  Report Jatuh Tempo
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.npl')?'active':''}}" href="{{route('reports.npl')}}">
                  Report NPL
                </a>
              </div>
            </li>
          </ul>
        </div>
      </div>
      @endrole
      @role('surveyor|spvsurveyor|')
      <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="{{route('survey.index')}}" >
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                  <i class="fa-solid fa-house"></i>
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
                <span class="nav-link-title">Report</span>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item {{request()->routeIs('reports.pembiayaan')?'active':''}}" href="{{route('reports.pembiayaan')}}">
                  Report Pembiayaan
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.pencairan')?'active':''}}" href="{{route('reports.pencairan')}}">
                  Report Pencairan
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.angsuran')?'active':''}}" href="{{route('reports.angsuran')}}">
                  Report Angsuran
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.pelunasan')?'active':''}}" href="{{route('reports.pelunasan')}}">
                  Report Pelunasan
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.outstanding')?'active':''}}" href="{{route('reports.outstanding')}}">
                  Report Outstanding
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.jatuhTempo')?'active':''}}" href="{{route('reports.jatuhTempo')}}">
                  Report Jatuh Tempo
                </a>
                <a class="dropdown-item {{request()->routeIs('reports.npl')?'active':''}}" href="{{route('reports.npl')}}">
                  Report NPL
                </a>
              </div>
            </li>
          </ul>
        </div>
      </div>
      @endrole
    </div>
  </header>