@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
   
            <div class="card">
                
                <div class="card-header bg-success">
                    <h3 class="card-title">Data Pembiayaan</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-striped">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>No Pengajuan</th>
                                    <th>Nama Debitur</th>
                                    <th>Marketing</th>
                                    <th>Cabang</th>
                                    <th>Status</th>
                                    <th width="220">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($pengajuans as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nomor_pengajuan }}</td>
                                    <td>{{ optional($item->nasabah)->nama }}</td>
                                    <td>{{ optional($item->marketing)->user->name }}</td>
                                    <td>{{ optional($item->cabang)->nama_cabang }}</td>
                                    <td>
                                        @if($item->pembiayaan)
                                            <span class="badge bg-green">
                                                Sudah Dibuat
                                            </span>
                                        @else
                                            <span class="badge bg-yellow text-dark">
                                                Belum Dibuat
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->pembiayaan)
                                            <a href="{{ route('pembiayaan.edit',$item->pembiayaan->id) }}"
                                                class="btn btn-warning btn-sm">
                                                Edit
                                            </a>
                                            <a href="{{ route('pembiayaan.review',$item->pembiayaan->id) }}"
                                                class="btn btn-success btn-sm">
                                                Review
                                            </a>
                                        @else
                                            <a href="{{ route('pembiayaan.create',$item->id) }}"
                                                class="btn btn-primary btn-sm">
                                                Buat Pembiayaan
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        Tidak ada data.
                                    </td>
                                </tr>
                            @endforelse
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