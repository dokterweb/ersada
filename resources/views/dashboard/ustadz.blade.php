@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Dashboard
            </h2>
          </div>
          <!-- Page title actions -->
          <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
              <a href="#" class="btn btn-primary d-none d-sm-inline-block">Create new report</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-deck row-cards">
          <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-primary text-white avatar">
                      <i class="fas fa-users" style="font-size: 24px;"></i>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      {{$totalSantriPutra}} Santri Putra
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-green text-white avatar">
                      <i class="fas fa-users" style="font-size: 24px;"></i>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      {{$totalSantriPutri}} Santri Putri
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-twitter text-white avatar">
                      <i class="fas fa-user-graduate" style="font-size: 24px;"></i>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      {{$totalUstadzPutra}} Total Ustadz
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <span class="bg-facebook text-white avatar">
                      <i class="fas fa-user-graduate" style="font-size: 24px;"></i>
                    </span>
                  </div>
                  <div class="col">
                    <div class="font-weight-medium">
                      {{$totalUstadzPutra}} Total Ustadz
                    </div>
                  
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row row-cards mt-2">
          <div class="col-md-12">
          <div class="card">
            <div class="card-header bg-cyan-lt">
              <h3 class="card-title">5 Data Murojaah Terbaru</h3>
            </div>
            <div class="card-body">
              <div class="table-responsive p-3">
                <table id="mytable" class="table table-vcenter card-table" style="width:100%;">
                  <thead>
                    <tr>
                        <th>Tgl Murojaah</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Kelamin</th>
                        <th>Ayat</th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($latestMurojaah as $m)
                          <tr>
                            <td>{{ \Carbon\Carbon::parse($m->tgl_murojaah)->format('d-m-Y') }}</td>
                            <td>{{ $m->santri->user->name }}</td>
                            <td>{{ $m->santri->kelasnya->nama_kelas ?? '-' }}</td>
                            <td>{{ $m->santri->kelamin ?? '-' }}</td>
                            <td>{{ $m->dariayat.' - '.$m->sampaiayat }}</td>
                          </tr>
                      @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection