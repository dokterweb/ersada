@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Data Karyawan
            </h2>
          </div>
          <!-- Page title actions -->
          <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
              <a href="{{route('karyawans.create')}}" class="btn btn-primary">
                Tambah karyawan
            </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-8">
            <div class="card">
              <div class="card-header bg-blue-lt">
                <h3 class="card-title">Detail karyawan</h3>
              </div>
              <div class="table-responsive p-3">
                <table class="table table-vcenter card-table table-striped">
                 <tbody>
                  <tr>
                    <td>Nama karyawan</td>
                    <td>{{$karyawan->user->name}}</td>
                  </tr>
                  <tr>
                    <td>Nama karyawan</td>
                    <td>{{$karyawan->user->email}}</td>
                  </tr>
                  <tr>
                      <td>Level Jabatan</td>
                      <td>
                        @php
                          $role = $karyawan->user->getRoleNames()->first();
                          $label = match($role) {
                              'kacab'         => 'Kepala Cabang',
                              'spvmarketing'  => 'SPV Marketing',
                              'marketing'     => 'Marketing',
                              'spvsurveyor'   => 'SPV Surveyor',
                              default => ucfirst($role ?? '-'),
                          };
                        @endphp
                        {{ $label }}
                      </td>
                  </tr>
                  <tr>
                      <td>Cabang</td>
                      <td>{{$karyawan->cabang->nama_cabang}}</td>
                  </tr>
                  <tr>
                      <td>Kelamin</td>
                      <td>{{$karyawan->kelamin}}</td>
                  </tr>
                  <tr>
                      <td>Tempat/Tgl Lahir</td>
                      <td>{{$karyawan->tempat_lahir.' / '.$karyawan->tgl_lahir}}</td>
                  </tr>
                  <tr>
                      <td>Alamat</td>
                      <td>{{$karyawan->alamat}}</td>
                  </tr>
                  <tr>
                      <td>Tanggal Masuk</td>
                      <td>{{$karyawan->tgl_masuk}}</td>
                  </tr>
                 <tr>
                      <td>No HP</td>
                      <td>{{$karyawan->no_hp}}</td>
                  </tr>
                   
                  </tbody>
                </table>
              </div>
              <div class="card-footer text-end">
                <a href="{{route('karyawans')}}" class="btn btn-info">Kembali</a>
                </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="card">
              <div class="card-header bg-blue-lt">
                <h3 class="card-title">Photo Karyawan</h3>
              </div>
              <div class="card-body">
                @if($karyawan->user->avatar)
                <img src="{{ asset('storage/'.$karyawan->user->avatar) }}" width="450"class="img-thumbnail mb-2">
                @endif
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection