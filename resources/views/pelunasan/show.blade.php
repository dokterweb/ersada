@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Detail Pelunasan
                </h2>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
            <div class="card">
                <div class="card-header bg-blue-lt">
                    <h3 class="card-title">Data Pengajuan Pembiayaan</h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-1">{{ $pelunasan->nomor_pelunasan }}</h3>
                            <h5 class="text-muted">
                                {{ $pelunasan->pembiayaan->pengajuan->nasabah->nama }}
                            </h5>
        
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-success fs-6">LUNAS</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <strong>Data Nasabah</strong>
                    </div>
                    <table class="table table-sm">
                        <tr>
                            <th width="35%">Nama</th>
                            <td>{{ $pelunasan->pembiayaan->pengajuan->nasabah->nama }}</td>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <td>{{ $pelunasan->pembiayaan->pengajuan->nasabah->nik }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $pelunasan->pembiayaan->pengajuan->nasabah->alamat }}</td>
                        </tr>
                        <tr>
                            <th>Marketing</th>
                            <td>{{ $pelunasan->pembiayaan->pengajuan->marketing->name }}</td>
                        </tr>
                        <tr>
                            <th>Cabang</th>
                            <td>{{ $pelunasan->pembiayaan->pengajuan->cabang->nama_cabang }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <strong>Data Pembiayaan</strong>
                    </div>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">No Pembiayaan</th>
                            <td>{{ $pelunasan->pembiayaan->nomor_pembiayaan }}</td>
                        </tr>
                        <tr>
                            <th>Plafond</th>
                            <td>Rp {{ number_format($pelunasan->pembiayaan->plafond,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <th>Tenor</th>
                            <td>{{ $pelunasan->pembiayaan->tenor }} Bulan</td>
                        </tr>
                        <tr>
                            <th>Tanggal Akad</th>
                            <td>{{ optional($pelunasan->pembiayaan->akad)->tanggal_akad?->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Pencairan</th>
                            <td>{{ optional(optional($pelunasan->pembiayaan->akad)->pencairan)->tanggal_pencairan?->format('d-m-Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= PELUNASAN ================= --}}
        <div class="card mt-3">
            <div class="card-header">
                <strong>Informasi Pelunasan</strong>
            </div>
            <table class="table table-bordered mb-0">
                <tr>
                    <th width="25%">Nomor Pelunasan</th>
                    <td>{{ $pelunasan->nomor_pelunasan }}</td>
                </tr>
                <tr>
                    <th>Tanggal Pelunasan</th>
                    <td>{{ $pelunasan->tanggal_pelunasan->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <th>Sisa Pokok</th>
                    <td>Rp {{ number_format($pelunasan->sisa_pokok,0,',','.') }}</td>
                </tr>
                <tr>
                    <th>Sisa Bunga</th>
                    <td>Rp {{ number_format($pelunasan->sisa_bunga,0,',','.') }}</td>
                </tr>
                <tr>
                    <th>Denda</th>
                    <td>Rp {{ number_format($pelunasan->denda,0,',','.') }}</td>
                </tr>
                <tr>
                    <th>Diskon</th>
                    <td>Rp {{ number_format($pelunasan->diskon,0,',','.') }}</td>
                </tr>
                <tr class="table-success">
                    <th>Total Pelunasan</th>
                    <th>Rp {{ number_format($pelunasan->total_pelunasan,0,',','.') }}</th>
                </tr>
                <tr>
                    <th>Keterangan</th>
                    <td>{{ $pelunasan->keterangan ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Petugas</th>
                    <td>{{ $pelunasan->creator->name }}</td>
                </tr>
            </table>
        </div>
        <div class="mt-3">
            <a href="{{ route('pelunasan.cetak',$pelunasan) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i>
                Cetak PDF
            </a>
            <a href="{{ route('operasional.show',$pelunasan->pembiayaan) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
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

    function deleteConfirmation(id) {
        // SweetAlert2 konfirmasi
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirimkan form untuk menghapus data jika dikonfirmasi
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection