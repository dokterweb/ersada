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
          <div class="col-md-12">
            <div class="card">
              <div class="card-header bg-cyan-lt">
                <h3 class="card-title">5 Data Murojaah Terbaru</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3 row">
                      <label class="col-3 col-form-label">Nama santri</label>
                      <div class="col">
                        <input type="text" class="form-control" value="{{ $detail->user->name }}" disabled>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label class="col-3 col-form-label">Nama Ustadz</label>
                      <div class="col">
                        <input type="text" class="form-control" value="{{ optional(optional($detail->ustadz)->user)->name ?? '-' }}" disabled>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label class="col-3 col-form-label">Kelompok</label>
                      <div class="col">
                        <input type="text" class="form-control" value="{{ $detail->kelompok->nama_kelompok }}" disabled>
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label class="col-3 col-form-label">Kelas</label>
                      <div class="col">
                        <input type="text" class="form-control" value="{{ $detail->kelasnya->nama_kelas }}" disabled>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 d-flex align-items-center justify-content-center">
                      <div class="text-center">
                          @if(!empty($detail->user->avatar))
                              <img src="{{ asset('storage/' . $detail->user->avatar) }}"
                                  alt="Avatar Santri"
                                  class="img-thumbnail rounded-circle"
                                  style="width: 200px; height: 200px; object-fit: cover;">
                          @else
                              <img src="{{ asset('images/default-avatar.png') }}"
                                  alt="Default Avatar"
                                  class="img-thumbnail rounded-circle"
                                  style="width: 200px; height: 200px; object-fit: cover;">
                          @endif
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