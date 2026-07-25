@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Modul Angsuran
                </h2>

                <div class="text-secondary">
                    Daftar pembiayaan yang sudah dicairkan
                </div>
            </div>
         
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <form method="GET" class="row w-100">
                        <div class="col-md-4">
                            <input type="text" name="keyword" class="form-control" placeholder="Cari Nomor Pembiayaan / Nama Debitur..."
                            value="{{ request('keyword') }}">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary"><i class="ti ti-search"></i>Cari</button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped card-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Pembiayaan</th>
                                <th>Nama Debitur</th>
                                <th>Marketing</th>
                                <th>Plafond</th>
                                <th>Tenor</th>
                                <th>Status</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembiayaans as $item)
                            <tr>
                                <td>{{ $loop->iteration + (($pembiayaans->currentPage()-1) * $pembiayaans->perPage()) }}</td>
                                <td>{{ $item->nomor_pembiayaan }}</td>
                                <td>{{ $item->pengajuan->nasabah->nama }}</td>
                                <td>{{ optional($item->pengajuan->marketing)->user->name }}</td>
                                <td>Rp {{ number_format($item->plafond,0,',','.') }}</td>
                                <td>{{ $item->tenor }} Bulan</td>
                                <td><span class="badge bg-success">{{ ucfirst($item->status) }}</span></td>
                                <td>
                                    <a href="{{ route('angsuran.show',$item) }}" class="btn btn-primary btn-sm">
                                    <i class="ti ti-credit-card"></i>
                                    Angsuran
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    Belum ada data.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $pembiayaans->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>

$(document).ready(function() {
    // Inisialisasi DataTables
    $('#mytable').DataTable({
        "processing": true,   // Menampilkan loading saat memproses data
        "serverSide": false,  // Tentukan apakah menggunakan server-side processing
        "paging": true,       // Menampilkan pagination
        "lengthChange": false // Menonaktifkan pengaturan jumlah baris per halaman
    });
});
</script>
@endsection