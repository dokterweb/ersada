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
        
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">

            @if($akad->file_word)
                <div class="alert alert-success">
                    Dokumen Akad telah berhasil dibuat.
                </div>
            @else
                <div class="alert alert-warning">
                    Dokumen belum dibuat.
                </div>
            @endif
        
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <strong>DATA DEBITUR</strong>
                    </div>
        
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="180">Nama</th>
                                <td>{{ optional($akad->pembiayaan->pengajuan->nasabah)->nama }}</td>
                            </tr>
        
                            <tr>
                                <th>NIK</th>
                                <td>{{ optional($akad->pembiayaan->pengajuan->nasabah)->nik }}</td>
                            </tr>
        
                            <tr>
                                <th>Alamat</th>
                                <td>{{ optional($akad->pembiayaan->pengajuan->nasabah)->alamat }}</td>
                            </tr>
        
                            <tr>
                                <th>Marketing</th>
                                <td>{{ optional(optional($akad->pembiayaan->pengajuan->marketing)->user)->name }}</td>
                            </tr>
        
                            <tr>
                                <th>Cabang</th>
                                <td>{{ optional($akad->pembiayaan->pengajuan->cabang)->nama_cabang }}</td>
                            </tr>
        
                        </table>
        
                    </div>
        
                </div>
        
            </div>
        
            {{-- ========================================================= --}}
            {{-- DATA PEMBIAYAAN --}}
            {{-- ========================================================= --}}
        
            <div class="col-lg-6">
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <strong>DATA PEMBIAYAAN</strong>
                    </div>
        
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="180">Nomor Pengajuan</th>
                                <td>{{ $akad->pembiayaan->pengajuan->nomor_pengajuan }}</td>
                            </tr>
                            <tr>
                                <th>Nomor Pembiayaan</th>
                                <td>{{ $akad->pembiayaan->nomor_pembiayaan }}</td>
                            </tr>
                            <tr>
                                <th>Nomor Akad</th>
                                <td>{{ $akad->nomor_akad }}</td>
                            </tr>
                            <tr>
                                <th>Plafond</th>
                                <td>Rp {{ number_format($akad->pembiayaan->plafond,0,',','.') }}</td>
                            </tr>
                            <tr>
                                <th>Tenor</th>
                                <td>{{ $akad->pembiayaan->tenor }} Bulan</td>
                            </tr>
                            <tr>
                                <th>Jenis Tenor</th>
                                <td>{{ ucfirst($akad->pembiayaan->jenis_tenor) }}</td>
                            </tr>
                            <tr>
                                <th>Dana Diterima</th>
                                <td>Rp {{ number_format($akad->pembiayaan->dana_diterima,0,',','.') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        
            {{-- ========================================================= --}}
            {{-- JADWAL ANGSURAN --}}
            {{-- ========================================================= --}}
        
            <div class="col-lg-12">
                <div class="card mb-3">
                    <div class="card-header bg-warning">
                        <strong>RINGKASAN ANGSURAN</strong>
                    </div>
                    <div class="card-body">
                        @php
                            $angsuranPertama = $akad->pembiayaan->angsurans->sortBy('angsuran_ke')->first();
                            $angsuranTerakhir = $akad->pembiayaan->angsurans->sortByDesc('angsuran_ke')->first();
                        @endphp
                        <div class="row">
                            <div class="col-md-3">
                                <div class="small text-secondary">
                                    Total Angsuran
                                </div>
                                <strong>{{ $akad->pembiayaan->angsurans->count() }} Kali</strong>
                            </div>
                            <div class="col-md-3">
                                <div class="small text-secondary">
                                    Angsuran / Bulan
                                </div>
                                <strong>
                                    Rp {{ number_format(optional($angsuranPertama)->total_angsuran,0,',','.') }}
                                </strong>
                            </div>
                            <div class="col-md-3">
                                <div class="small text-secondary">
                                    Jatuh Tempo Pertama
                                </div>
                                <strong>{{ optional($angsuranPertama)->tanggal_jatuh_tempo }}</strong>
                            </div>
                            <div class="col-md-3">
                                <div class="small text-secondary">
                                    Jatuh Tempo Terakhir
                                </div>
                                <strong>{{ optional($angsuranTerakhir)->tanggal_jatuh_tempo }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            {{-- ========================================================= --}}
            {{-- DOKUMEN --}}
            {{-- ========================================================= --}}
        
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <strong>DOKUMEN AKAD</strong>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            Klik <strong>Generate Dokumen</strong> untuk membuat file Akad secara otomatis.
                        </div>
                        <div class="d-flex gap-2 flex-wrap">

                            @if(!$akad->file_word)
                                <a href="{{ route('akad.generate',$akad) }}" class="btn btn-success">
                                    <i class="ti ti-file-word"></i>
                                    Generate Dokumen
                                </a>
                            @else
                                <a href="{{ route('akad.generate',$akad) }}" class="btn btn-warning">
                                    <i class="ti ti-refresh"></i>
                                    Generate Ulang
                                </a>
                                <a href="{{ asset('storage/akad/'.$akad->file_word) }}" target="_blank" class="btn btn-primary">
                                    <i class="ti ti-download"></i>
                                    Download Word
                                </a>
                                <a href="#" class="btn btn-danger"> 
                                    <i class="ti ti-file-type-pdf"></i>
                                    Generate PDF
                                </a>
                            @endif
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 d-flex justify-content-between">
            <a href="{{ route('pembiayaan.index') }}" class="btn btn-secondary">
                Kembali
            </a>
            @if(!$akad->pencairan)
                <a href="{{ route('pencairan.create',$akad) }}"
                    class="btn btn-success">
                    <i class="ti ti-cash"></i>
                    Pencairan
                </a>
            @else
                <a href="{{ route('pencairan.show',$akad->pencairan) }}"
                    class="btn btn-info">
                    <i class="ti ti-eye"></i>
                    Lihat Pencairan
                </a>
            @endif
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