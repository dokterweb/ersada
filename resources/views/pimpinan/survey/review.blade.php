@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Final Review Survey
            </h3>

            <div class="text-muted">
                Pengajuan:
                <strong>
                    {{ $pengajuan->nomor_pengajuan }}
                </strong>
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
    {{-- NAVIGASI --}}
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

    <div class="row">
        {{-- ========================================================= --}}
        {{-- STATUS SURVEY --}}
        {{-- ========================================================= --}}
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Status Survey</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <table class="table table-sm">
                            <tr>
                                <td>Status</td>
                                <td>
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
                                </td>
                            </tr>
                            <tr>
                                <td>Surveyor</td>
                                <td>
                                {{ $survey->assignedTo?->name ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Mulai Survey</td>
                                <td>
                                    {{ $survey->started_at
                                        ? \Carbon\Carbon::parse($survey->started_at)->format('d/m/Y H:i')
                                        : '-'
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td>Selesai Survey</td>
                                <td>
                                    {{ $survey->finished_at
                                        ? \Carbon\Carbon::parse($survey->finished_at)->format('d/m/Y H:i')
                                        : '-'
                                    }}
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>

            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- DATA NASABAH --}}
        {{-- ========================================================= --}}
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0">Data Nasabah</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <table class="table table-sm">
                            <tr>
                                <td>Nama Nasabah</td>
                                <td>{{ $pengajuan->nasabah?->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>NIK</td>
                                <td>{{ $pengajuan->nasabah?->nik ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>No. HP</td>
                                <td>
                                    {{ $pengajuan->nasabah?->no_hp ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>{{ $pengajuan->nasabah?->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Status Perkawinan</td>
                                <td>{{ $pengajuan->nasabah?->status_perkawinan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Penghasilan</td>
                                <td> Rp {{ format_rupiah($pengajuan->nasabah?->pekerjaanNasabah?->penghasilan ?? 0) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    
        {{-- ========================================================= --}}
        {{-- HASIL BERKAS --}}
        {{-- ========================================================= --}}
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Hasil Pemeriksaan Berkas</h4>
                </div>

                <div class="card-body">
                    @if($survey->berkas)
                        <div class="row">
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
                                    <td>{{ $survey->berkas->status_peminjam ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Plafond Pinjaman Lama</td>
                                    <td>Rp {{ format_rupiah($survey->berkas->plafond_pinjaman_lama ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <td>Catatan Kekurangan</td>
                                    <td>{{ $survey->berkas->catatan_kekurangan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>

                    @else

                        <div class="alert alert-warning mb-0">
                            Data berkas survey belum tersedia.
                        </div>

                    @endif

                </div>

            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- DOKUMENTASI RUMAH --}}
    {{-- ========================================================= --}}

    <div class="card mb-4">

        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                Dokumentasi Rumah
            </h5>
        </div>

        <div class="card-body">

            @if($rumah->count())

                <div class="row">

                    @foreach($rumah as $item)

                        <div class="col-md-4 mb-4">

                            <div class="card h-100">

                                <a href="{{ asset('storage/'.$item->file) }}"
                                   target="_blank">

                                    <img
                                        src="{{ asset('storage/'.$item->file) }}"
                                        class="card-img-top"
                                        style="height:250px; object-fit:cover;"
                                        alt="Dokumentasi Rumah"
                                    >

                                </a>

                                <div class="card-body">

                                    <strong>
                                        {{ ucfirst($item->posisi ?? 'Dokumentasi') }}
                                    </strong>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="alert alert-secondary mb-0">
                    Tidak ada dokumentasi rumah.
                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DOKUMENTASI USAHA --}}
    {{-- ========================================================= --}}

    <div class="card mb-4">

        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                Dokumentasi Usaha
            </h5>
        </div>

        <div class="card-body">

            @if($usaha->count())

                <div class="row">

                    @foreach($usaha as $item)

                        <div class="col-md-4 mb-4">

                            <div class="card h-100">

                                <a href="{{ asset('storage/'.$item->file) }}"
                                   target="_blank">

                                    <img
                                        src="{{ asset('storage/'.$item->file) }}"
                                        class="card-img-top"
                                        style="height:250px; object-fit:cover;"
                                        alt="Dokumentasi Usaha"
                                    >

                                </a>

                                <div class="card-body">

                                    <strong>
                                        Dokumentasi Usaha
                                    </strong>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="alert alert-secondary mb-0">
                    Tidak ada dokumentasi usaha.
                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DOKUMENTASI JAMINAN --}}
    {{-- ========================================================= --}}

    <div class="card mb-4">

        <div class="card-header bg-warning">
            <h5 class="mb-0">
                Dokumentasi Jaminan
            </h5>
        </div>

        <div class="card-body">

            @if($jaminan->count())

                <div class="row">

                    @foreach($jaminan as $item)

                        <div class="col-md-4 mb-4">

                            <div class="card h-100">

                                <a href="{{ asset('storage/'.$item->file) }}"
                                   target="_blank">

                                    <img
                                        src="{{ asset('storage/'.$item->file) }}"
                                        class="card-img-top"
                                        style="height:250px; object-fit:cover;"
                                        alt="Dokumentasi Jaminan"
                                    >

                                </a>

                                <div class="card-body">

                                    <div class="fw-bold">
                                        {{ $item->posisi ?? 'Jaminan' }}
                                    </div>

                                    @if($item->jaminanPengajuan)
                                        <div class="small text-muted">
                                            {{ $item->jaminanPengajuan->jenis_jaminan }}
                                        </div>
                                    @endif

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="alert alert-secondary mb-0">
                    Tidak ada dokumentasi jaminan.
                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- VIDEO SURVEY --}}
    {{-- ========================================================= --}}

    <div class="card mb-4">

        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                Video Survey
            </h5>
        </div>

        <div class="card-body">

            @if($videos->count())

                <div class="row">

                    @foreach($videos as $video)

                        <div class="col-md-6 mb-4">

                            <div class="card">

                                <div class="card-body">

                                    <video
                                        controls
                                        class="w-100 rounded"
                                        style="max-height:500px;"
                                    >

                                        <source
                                            src="{{ asset('storage/'.$video->file) }}"
                                            type="video/mp4"
                                        >

                                        Browser tidak mendukung video.

                                    </video>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="alert alert-secondary mb-0">
                    Tidak ada video survey.
                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- CATATAN REVIEW --}}
    {{-- ========================================================= --}}

    <div class="card mb-4">

        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">
                Catatan Review Survey
            </h5>
        </div>

        <div class="card-body">

            @if($survey->review_note)

                <div class="border rounded p-3 bg-light">

                    {!! nl2br(e($survey->review_note)) !!}

                </div>

            @else

                <div class="text-muted">
                    Belum ada catatan review.
                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- FOOTER --}}
    {{-- ========================================================= --}}

    <div class="text-end mb-4">

        <a href="{{ route('pimpinan.show', $pengajuan->id) }}"
           class="btn btn-secondary">

            <i class="fa fa-arrow-left"></i>
            Kembali ke Pengajuan

        </a>

    </div>

</div>

@endsection