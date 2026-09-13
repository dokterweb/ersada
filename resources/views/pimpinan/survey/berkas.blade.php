@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-1">
                Hasil Survey - Berkas
            </h3>

            <div class="text-muted">
                Pengajuan:
                <strong>{{ $pengajuan->nomor_pengajuan }}</strong>
            </div>
        </div>

        <div>
            <a href="{{ route('pimpinan.show', $pengajuan->id) }}"
               class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i>
                Kembali
            </a>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- NAVIGASI SURVEY --}}
    {{-- ========================================================= --}}

    <div class="card mb-4">

        <div class="card-body">

            <div class="d-flex gap-2 flex-wrap">

                <a href="{{ route('pimpinan.survey.berkas', $pengajuan->id) }}"
                   class="btn btn-primary">
                    <i class="fa fa-folder-open"></i>
                    Berkas
                </a>

                <a href="{{ route('pimpinan.survey.review', $pengajuan->id) }}"
                   class="btn btn-success">
                    <i class="fa fa-eye"></i>
                    Review Survey
                </a>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- INFORMASI SURVEY --}}
    {{-- ========================================================= --}}

    <div class="card mb-4">

        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                Informasi Survey
            </h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">
                        Nomor Pengajuan
                    </label>

                    <div>
                        {{ $pengajuan->nomor_pengajuan }}
                    </div>
                </div>


                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">
                        Nama Nasabah
                    </label>

                    <div>
                        {{ $pengajuan->nasabah?->nama ?? '-' }}
                    </div>
                </div>


                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">
                        Surveyor
                    </label>

                    <div>
                        {{ $survey->assignedTo?->name ?? '-' }}
                    </div>
                </div>


                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">
                        Status Survey
                    </label>

                    <div>

                        @php
                            $statusClass = match($survey->status) {
                                'submitted' => 'bg-success',
                                'reviewed' => 'bg-primary',
                                'revision' => 'bg-warning text-dark',
                                'rejected' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        @endphp

                        <span class="badge {{ $statusClass }}">
                            {{ ucfirst($survey->status) }}
                        </span>

                    </div>
                </div>


                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">
                        Mulai Survey
                    </label>

                    <div>
                        {{ $survey->started_at
                            ? \Carbon\Carbon::parse($survey->started_at)->format('d/m/Y H:i')
                            : '-'
                        }}
                    </div>
                </div>


                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">
                        Selesai Survey
                    </label>

                    <div>
                        {{ $survey->finished_at
                            ? \Carbon\Carbon::parse($survey->finished_at)->format('d/m/Y H:i')
                            : '-'
                        }}
                    </div>
                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- HASIL SURVEY BERKAS --}}
    {{-- ========================================================= --}}

    <div class="card mb-4">

        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                Hasil Survey Berkas
            </h5>
        </div>


        <div class="card-body">

            @if($survey->berkas)

                <div class="row">
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <tr>
                                <td>KTP Debitur</td>
                                <td>
                                    @if($survey->berkas->ktp_debitur)
                                        <span class="badge bg-success">Ada</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>KK Debitur</td>
                                <td>
                                    @if($survey->berkas->kk_debitur)
                                        <span class="badge bg-success">Ada</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>KTP Pasangan</td>
                                <td>
                                    @if($survey->berkas->ktp_pasangan)
                                        <span class="badge bg-success">Ada</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>KK Pasangan</td>
                                <td>
                                    @if($survey->berkas->kk_pasangan)
                                        <span class="badge bg-success">Ada</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Status Peminjam</td>
                                <td>
                                   {{ $survey->berkas->status_peminjam ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Plafond Pinjaman Lama</td>
                                <td>
                                   Rp {{ format_rupiah($survey->berkas->plafond_pinjaman_lama ?? 0) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <tr>
                                <td>KTP Penjamin</td>
                                <td>
                                    @if($survey->berkas->ktp_penjamin)
                                        <span class="badge bg-success">Ada</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>KK Penjamin</td>
                                <td>
                                   @if($survey->berkas->kk_penjamin)
                                        <span class="badge bg-success">Ada</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Nama Pasangan / Penjamin</td>
                                <td>
                                   {{ $survey->berkas->nama_pasangan_penjamin ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td>KK Pasangan Penjamin</td>
                                <td>
                                    @if($survey->berkas->kk_pasangan_penjamin)
                                        <span class="badge bg-success">Ada</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <tr>
                                <td>Slip Gaji</td>
                                <td>
                                    @if($survey->berkas->slip_gaji)
                                        <span class="badge bg-success">Ada</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>No. ID Karyawan</td>
                                <td>
                                  {{ $survey->berkas->no_id_karyawan ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Lama Bekerja</td>
                                <td>
                                   {{ $survey->berkas->lama_bekerja ?? 0 }} bulan
                                </td>
                            </tr>
                            <tr>
                                <td>No. Telp Karyawan</td>
                                <td>
                                    {{ $survey->berkas->no_telp_karyawan ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td>BPJS</td>
                                <td>
                                    @if($survey->berkas->bpjs)
                                        <span class="badge bg-success">Ada</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>NO BPJS</td>
                                <td>
                                   {{ $survey->berkas->no_bpjs ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Buku Tabungan</td>
                                <td>
                                   @if($survey->berkas->buku_tabungan)
                                        <span class="badge bg-success">Ada</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Nama Bank</td>
                                <td>
                                   {{ $survey->berkas->nama_bank ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Kartu ATM</td>
                                <td>
                                    @if($survey->berkas->kartu_atm)
                                        <span class="badge bg-success">Ada</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Ada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>PIN ATM</td>
                                <td>
                                   {{ $survey->berkas->pin_atm ?? '-' }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- CATATAN KEKURANGAN --}}

                    <div class="col-md-12 mb-3">

                        <label class="form-label fw-bold">
                            Catatan Kekurangan
                        </label>

                        <div class="border rounded p-3 bg-light">

                            {!! nl2br(
                                e($survey->berkas->catatan_kekurangan ?? '-')
                            ) !!}

                        </div>

                    </div>

                </div>

            @else

                <div class="alert alert-warning mb-0">
                    Data hasil survey berkas belum tersedia.
                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DOKUMEN PENGAJUAN --}}
    {{-- ========================================================= --}}

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">
                Dokumen Pengajuan
            </h5>
        </div>

        <div class="card-body">

            @if($pengajuan->dokumenPengajuans->count())

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead>
                            <tr>
                                <th width="40">No</th>
                                <th>Jenis Dokumen</th>
                                <th>Nama File</th>
                                <th>Status</th>
                                <th width="100">Lihat</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($pengajuan->dokumenPengajuans as $doc)

                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        {{ $doc->jenis_dokumen }}
                                    </td>

                                    <td>
                                        {{ $doc->nama_file }}
                                    </td>

                                    <td>
                                        <span class="badge bg-success">
                                            {{ $doc->status }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($doc->file_path)

                                            <a href="{{ asset('storage/'.$doc->file_path) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye"></i>
                                            </a>

                                        @endif
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="alert alert-secondary mb-0">
                    Tidak ada dokumen pengajuan.
                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DOKUMEN PAYROLL --}}
    {{-- ========================================================= --}}

    @if($pengajuan->dokumenPayrolls->count())

        <div class="card mb-4">

            <div class="card-header">
                <h5 class="mb-0">
                    Dokumen Payroll
                </h5>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Dokumen</th>
                                <th>File</th>
                                <th>Lihat</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($pengajuan->dokumenPayrolls as $doc)

                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        {{ $doc->jenis_dokumen ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $doc->nama_file ?? '-' }}
                                    </td>

                                    <td>

                                        @if($doc->file_path)

                                            <a href="{{ asset('storage/'.$doc->file_path) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye"></i>
                                                Lihat
                                            </a>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    @endif


</div>

@endsection