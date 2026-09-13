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
        
            <div class="col-lg-6">
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
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="180">Total Angsuran</th>
                                    <td>{{ $akad->pembiayaan->angsurans->count() }} Kali</td>
                                </tr>
                                <tr>
                                    <th>Angsuran / Bulan</th>
                                    <td>Rp {{ number_format(optional($angsuranPertama)->total_angsuran,0,',','.') }}</td>
                                </tr>
                                <tr>
                                    <th>Jatuh Tempo Pertama</th>
                                    <td>{{ \Carbon\Carbon::parse(optional($angsuranPertama)->tanggal_jatuh_tempo)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Jatuh Tempo Terakhir</th>
                                    <td>{{ \Carbon\Carbon::parse(optional($angsuranTerakhir)->tanggal_jatuh_tempo)->format('d M Y') }}</td>
                                </tr>    
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-danger">
                        <strong>STATUS AKAD</strong>
                    </div>
                    <div class="card-body">
                        @if($akad->status=='draft')
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="badge bg-warning p-3 rounded-circle">
                                        <i class="ti ti-clock fs-4"></i>
                                    </span>
                                </div>
                                <div>
                                    <h5 class="text-warning mb-1">Draft</h5>
                                    <p class="mb-2 text-muted">
                                        Dokumen akad telah dibuat dan siap dicetak.
                                        Menunggu proses tanda tangan nasabah.
                                    </p>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td width="140">Dibuat</td>
                                            <td>{{ $akad->created_at->format('d M Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nomor Akad</td>
                                            <td>{{ $akad->nomor_akad }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @elseif($akad->status=='signed')
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="badge bg-success p-3 rounded-circle">
                                        <i class="ti ti-circle-check fs-4"></i>
                                    </span>
                                </div>
                                <div>
                                    <h5 class="text-success mb-1">Akad Sudah Ditandatangani</h5>
                                    <p class="mb-2 text-muted">
                                        Dokumen akad telah selesai ditandatangani.
                                        Pembiayaan siap diproses ke tahap pencairan.
                                    </p>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td width="140">Tanggal Akad</td>
                                            <td>{{ \Carbon\Carbon::parse($akad->tanggal_akad)->format('d M Y') }}</td>
                                        </tr>
                                        @if(isset($akad->signed_at))
                                        <tr>
                                            <td>Ditandatangani</td>
                                            <td>{{ $akad->signed_at->format('d M Y H:i') }}</td>
                                        </tr>
                                        @endif
                                        @if(isset($akad->signedBy))
                                        <tr>
                                            <td>Dikonfirmasi Oleh</td>
                                            <td>{{ $akad->signedBy->name }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        @endif
                        <hr>
                        <div class="row text-center">
                            <div class="col">
                                <div class="fw-bold {{ in_array($akad->status,['draft','signed']) ? 'text-success' : 'text-muted' }}">
                                    ✓ Akad Dibuat
                                </div>
                            </div>
                            <div class="col">
                                <div class="fw-bold {{ $akad->status=='signed' ? 'text-success' : 'text-muted' }}">
                                    ✓ Ditandatangani
                                </div>
                            </div>
                            <div class="col">
                                <div class="fw-bold {{ optional($akad->pembiayaan)->status=='dicairkan' ? 'text-success' : 'text-muted' }}">
                                    ✓ Pencairan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DOKUMEN --}}
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
                               {{--  <a href="#" class="btn btn-danger"> 
                                    <i class="ti ti-file-type-pdf"></i>
                                    Generate PDF
                                </a> --}}
                            @endif
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
       {{--  <div class="mt-4 d-flex justify-content-between">
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
        </div> --}}

        <div class="mt-4 d-flex justify-content-between">
            <a href="{{ route('pembiayaan.index') }}"class="btn btn-secondary">
                Kembali
            </a>
            <div>
                {{-- <a href="{{ route('akad.cetak', $akad->id) }}" class="btn btn-primary">
                    <i class="ti ti-printer"></i>
                    Cetak Akad
                </a> --}}
                @if($akad->status=='draft')
                    <form action="{{ route('akad.sign',$akad) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-warning" onclick="return confirm('Pastikan akad sudah ditandatangani?')">
                            <i class="ti ti-check"></i>
                            Akad Ditandatangani
                        </button>
                    </form>
                @elseif(!$akad->pembiayaan->pencairan)
                    <a href="{{ route('pencairan.create',$akad) }}" class="btn btn-success">
                        <i class="ti ti-cash"></i>
                        Pencairan
                    </a>
                @else
                    <a href="{{ route('pencairan.show',$akad->pembiayaan->pencairan) }}" class="btn btn-info">
                        <i class="ti ti-eye"></i>
                        Lihat Pencairan
                    </a>
                @endif
            </div>
        </div>
      </div>
    </div>
</div>
@endsection
