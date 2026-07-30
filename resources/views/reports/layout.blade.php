@extends('layouts.app')

@section('title')
    @yield('report-title')
@endsection

@section('content')
<div class="page-wrapper">
    <div class="page-header d-print-none mb-3">
        <div class="container-xl">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        @yield('report-title')
                    </h2>
                    <div class="text-secondary">
                        Modul Laporan Operasional Pembiayaan
                    </div>
                </div>
                <div class="col-auto">
                    @yield('header-action')
                </div>
            </div>
        </div>
    </div>

    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                {{-- FILTER --}}
                @include('reports.partials.filter')
                {{-- CONTENT --}}
                <div class="card">
                    <div class="card-body p-0">
                        @yield('report-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection