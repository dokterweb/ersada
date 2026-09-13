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
            <form action="{{ route('pengajuan.storeJaminan',$pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <ul class="steps steps-green steps-counter my-4">
                            <li class="step-item">STEP 1</li>
                            <li class="step-item">STEP 2</li>
                            <li class="step-item">STEP 3</li>
                            <li class="step-item">STEP 4</li>
                            <li class="step-item">REVIEW</li>
                            <li class="step-item">ANALISA</li>
                            <li class="step-item active">JAMINAN</li>
                            <li class="step-item">ANALISA KAPITAL</li>
                            <li class="step-item">REVIEW FINAL</li>
                        </ul>
        
                    </div>
                    <div class="card-header bg-success">
                        <h3 class="card-title">DATA JAMINAN</h3>
                    </div>
                  <div class="card-body">

                <button
                    type="button"
                    id="addJaminan"
                    class="btn btn-success mb-4"
                >
                    + Tambah Jaminan
                </button>


                <div id="jaminan_wrapper">

                    @foreach($jaminans as $index => $jaminan)

                        <div
                            class="card border mb-4 jaminan-item"
                            data-index="{{ $index }}"
                        >

                            <div class="card-header bg-light d-flex justify-content-between align-items-center">

                                <strong>
                                    Jaminan {{ $index + 1 }}
                                </strong>

                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm removeJaminan"
                                >
                                    Hapus Jaminan
                                </button>

                            </div>


                            <div class="card-body">

                                {{-- ID JAMINAN --}}
                                <input
                                    type="hidden"
                                    name="jaminan[{{ $index }}][id]"
                                    value="{{ $jaminan->id }}"
                                >


                                {{-- JENIS JAMINAN --}}
                                <div class="row mb-4">

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Jenis Jaminan <span class="text-danger">*</span>
                                        </label>

                                        <select
                                            name="jaminan[{{ $index }}][jenis_jaminan]"
                                            class="form-select jenis-jaminan"
                                        >

                                            <option value="">
                                                -- Pilih Jenis Jaminan --
                                            </option>

                                            <option value="BPKB Motor"
                                                {{ $jaminan->jenis_jaminan == 'BPKB Motor' ? 'selected' : '' }}>
                                                BPKB Motor
                                            </option>

                                            <option value="BPKB Mobil"
                                                {{ $jaminan->jenis_jaminan == 'BPKB Mobil' ? 'selected' : '' }}>
                                                BPKB Mobil
                                            </option>

                                            <option value="Surat Tanah"
                                                {{ $jaminan->jenis_jaminan == 'Surat Tanah' ? 'selected' : '' }}>
                                                Surat Tanah
                                            </option>
                                            <option value="SK Kerja"
                                                {{ $jaminan->jenis_jaminan == 'SK Kerja' ? 'selected' : '' }}>
                                                SK Kerja
                                            </option>
                                        </select>

                                    </div>

                                </div>


                                {{-- ================================================= --}}
                                {{-- DATA KENDARAAN --}}
                                {{-- ================================================= --}}

                                <div
                                    class="data-kendaraan"
                                    style="{{ in_array($jaminan->jenis_jaminan, ['BPKB Motor','BPKB Mobil']) ? '' : 'display:none;' }}"
                                >

                                    <div class="alert alert-primary">
                                        <strong>DATA KENDARAAN</strong>
                                    </div>


                                    <div class="row">

                                        {{-- Jenis Kendaraan --}}
                                        <div class="col-md-3 mb-3">

                                            <label class="form-label">
                                                Jenis Kendaraan
                                            </label>

                                            <select
                                                name="jaminan[{{ $index }}][jenis_kendaraan]"
                                                class="form-select"
                                            >

                                                <option value="">
                                                    -- Pilih --
                                                </option>

                                                <option
                                                    value="motor"
                                                    {{ $jaminan->jenis_kendaraan == 'motor' ? 'selected' : '' }}
                                                >
                                                    Motor
                                                </option>

                                                <option
                                                    value="mobil"
                                                    {{ $jaminan->jenis_kendaraan == 'mobil' ? 'selected' : '' }}
                                                >
                                                    Mobil
                                                </option>

                                            </select>

                                        </div>


                                        {{-- Tahun --}}
                                        <div class="col-md-3 mb-3">

                                            <label class="form-label">
                                                Tahun
                                            </label>

                                            <input
                                                type="number"
                                                name="jaminan[{{ $index }}][tahun_kendaraan]"
                                                class="form-control"
                                                value="{{ $jaminan->tahun_kendaraan }}"
                                                min="1900"
                                                max="{{ date('Y') + 1 }}"
                                            >

                                        </div>


                                        {{-- Merk --}}
                                        <div class="col-md-3 mb-3">

                                            <label class="form-label">
                                                Merk
                                            </label>

                                            <input
                                                type="text"
                                                name="jaminan[{{ $index }}][merk_kendaraan]"
                                                class="form-control"
                                                value="{{ $jaminan->merk_kendaraan }}"
                                            >

                                        </div>


                                        {{-- Plat Polisi --}}
                                        <div class="col-md-3 mb-3">

                                            <label class="form-label">
                                                Plat Polisi
                                            </label>

                                            <input
                                                type="text"
                                                name="jaminan[{{ $index }}][plat_polisi]"
                                                class="form-control"
                                                value="{{ $jaminan->plat_polisi }}"
                                            >

                                        </div>


                                        {{-- BPKB --}}
                                        <div class="col-md-4 mb-3">

                                            <label class="form-label">
                                                BPKB
                                            </label>

                                            <div>

                                                <label class="me-3">
                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][bpkb_status]"
                                                        value="ada"
                                                        {{ $jaminan->bpkb_status == 'ada' ? 'checked' : '' }}
                                                    >
                                                    Ada
                                                </label>

                                                <label>
                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][bpkb_status]"
                                                        value="tidak_ada"
                                                        {{ $jaminan->bpkb_status == 'tidak_ada' ? 'checked' : '' }}
                                                    >
                                                    Tidak Ada
                                                </label>

                                            </div>

                                        </div>


                                        {{-- Pajak STNK --}}
                                        <div class="col-md-4 mb-3">

                                            <label class="form-label">
                                                Pajak STNK
                                            </label>

                                            <div>

                                                <label class="me-3">

                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][pajak_stnk_status]"
                                                        value="ada"
                                                        {{ $jaminan->pajak_stnk_status == 'ada' ? 'checked' : '' }}
                                                    >

                                                    Ada

                                                </label>

                                                <label>

                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][pajak_stnk_status]"
                                                        value="tidak_ada"
                                                        {{ $jaminan->pajak_stnk_status == 'tidak_ada' ? 'checked' : '' }}
                                                    >

                                                    Tidak Ada

                                                </label>

                                            </div>

                                        </div>


                                        {{-- Status Pajak --}}
                                        <div class="col-md-4 mb-3">

                                            <label class="form-label">
                                                Status Pajak
                                            </label>

                                            <div>

                                                <label class="me-3">

                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][status_pajak]"
                                                        value="hidup"
                                                        {{ $jaminan->status_pajak == 'hidup' ? 'checked' : '' }}
                                                    >

                                                    Hidup

                                                </label>

                                                <label>

                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][status_pajak]"
                                                        value="mati"
                                                        {{ $jaminan->status_pajak == 'mati' ? 'checked' : '' }}
                                                    >

                                                    Mati

                                                </label>

                                            </div>

                                        </div>


                                        {{-- BPKB Atas Nama --}}
                                        <div class="col-md-3 mb-3">

                                            <label class="form-label">
                                                BPKB Atas Nama
                                            </label>

                                            <input
                                                type="text"
                                                name="jaminan[{{ $index }}][bpkb_atas_nama]"
                                                class="form-control"
                                                value="{{ $jaminan->bpkb_atas_nama }}"
                                            >

                                        </div>


                                        {{-- No BPKB --}}
                                        <div class="col-md-3 mb-3">

                                            <label class="form-label">
                                                No BPKB
                                            </label>

                                            <input
                                                type="text"
                                                name="jaminan[{{ $index }}][no_bpkb]"
                                                class="form-control"
                                                value="{{ $jaminan->no_bpkb }}"
                                            >

                                        </div>


                                        {{-- No Rangka --}}
                                        <div class="col-md-3 mb-3">

                                            <label class="form-label">
                                                No Rangka
                                            </label>

                                            <input
                                                type="text"
                                                name="jaminan[{{ $index }}][no_rangka]"
                                                class="form-control"
                                                value="{{ $jaminan->no_rangka }}"
                                            >

                                        </div>


                                        {{-- No Mesin --}}
                                        <div class="col-md-3 mb-3">

                                            <label class="form-label">
                                                No Mesin
                                            </label>

                                            <input
                                                type="text"
                                                name="jaminan[{{ $index }}][no_mesin]"
                                                class="form-control"
                                                value="{{ $jaminan->no_mesin }}"
                                            >

                                        </div>

                                    </div>

                                </div>


                                {{-- ================================================= --}}
                                {{-- DATA SURAT TANAH --}}
                                {{-- ================================================= --}}

                                <div class="data-tanah"
                                    style="{{ $jaminan->jenis_jaminan == 'Surat Tanah' ? '' : 'display:none;' }}">
                                    <div class="alert alert-warning">
                                        <strong>DATA SURAT TANAH</strong>
                                    </div>
                                    <div class="row">

                                        {{-- SKT/SPGR --}}
                                        <div class="col-md-4 mb-3">

                                            <label class="form-label">
                                                SKT/SPGR
                                            </label>

                                            <div>

                                                <label class="me-3">

                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][skt_spgr_status]"
                                                        value="ada"
                                                        {{ $jaminan->skt_spgr_status == 'ada' ? 'checked' : '' }}
                                                    >

                                                    Ada

                                                </label>

                                                <label>

                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][skt_spgr_status]"
                                                        value="tidak_ada"
                                                        {{ $jaminan->skt_spgr_status == 'tidak_ada' ? 'checked' : '' }}
                                                    >

                                                    Tidak Ada

                                                </label>

                                            </div>

                                        </div>


                                        {{-- Dikeluarkan Oleh --}}
                                        <div class="col-md-8 mb-3">

                                            <label class="form-label">
                                                SKT/SPGR Dikeluarkan Oleh
                                            </label>

                                            <div>

                                                <label class="me-3">

                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][skt_spgr_dikeluarkan_oleh]"
                                                        value="camat"
                                                        {{ $jaminan->skt_spgr_dikeluarkan_oleh == 'camat' ? 'checked' : '' }}
                                                    >

                                                    Camat

                                                </label>

                                                <label class="me-3">

                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][skt_spgr_dikeluarkan_oleh]"
                                                        value="kepala_desa"
                                                        {{ $jaminan->skt_spgr_dikeluarkan_oleh == 'kepala_desa' ? 'checked' : '' }}
                                                    >

                                                    Kepala Desa

                                                </label>

                                                <label class="me-3">

                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][skt_spgr_dikeluarkan_oleh]"
                                                        value="kepala_dusun"
                                                        {{ $jaminan->skt_spgr_dikeluarkan_oleh == 'kepala_dusun' ? 'checked' : '' }}
                                                    >

                                                    Kepala Dusun

                                                </label>

                                                <label>

                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][skt_spgr_dikeluarkan_oleh]"
                                                        value="bawah_tangan"
                                                        {{ $jaminan->skt_spgr_dikeluarkan_oleh == 'bawah_tangan' ? 'checked' : '' }}
                                                    >

                                                    Bawah Tangan

                                                </label>

                                            </div>

                                        </div>


                                        {{-- Sertifikat --}}
                                        <div class="col-md-4 mb-3">

                                            <label class="form-label">
                                                Sertifikat
                                            </label>

                                            <div>

                                                <label class="me-3">

                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][sertifikat_status]"
                                                        value="ada"
                                                        {{ $jaminan->sertifikat_status == 'ada' ? 'checked' : '' }}
                                                    >

                                                    Ada

                                                </label>

                                                <label>

                                                    <input
                                                        type="radio"
                                                        name="jaminan[{{ $index }}][sertifikat_status]"
                                                        value="tidak_ada"
                                                        {{ $jaminan->sertifikat_status == 'tidak_ada' ? 'checked' : '' }}
                                                    >

                                                    Tidak Ada

                                                </label>

                                            </div>

                                        </div>

                                    </div>
                                </div>

                                {{-- ================================================= --}}
                                {{-- DATA SK KERJA --}}
                                {{-- ================================================= --}}

                                <div class="data-sk-kerja"
                                    style="{{ $jaminan->jenis_jaminan == 'SK Kerja' ? '' : 'display:none;' }}">
                                    <div class="alert alert-success">
                                        <strong>DATA SK KERJA</strong>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nomor SK Kerja
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text" name="jaminan[{{ $index }}][no_sk_kerja]" class="form-control"
                                                value="{{ old(
                                                    "jaminan.$index.no_sk_kerja",
                                                    $jaminan->no_sk_kerja
                                                ) }}"
                                                placeholder="Masukkan nomor SK Kerja"
                                            >

                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="alert alert-info mb-0">
                                                <strong>Dokumen SK Kerja</strong>
                                                <br>
                                                Upload SK Kerja dalam bentuk:
                                                <br>
                                                JPG, JPEG, PNG atau PDF.
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                {{-- ================================================= --}}
                                {{-- NILAI & DESKRIPSI --}}
                                {{-- ================================================= --}}

                                <hr>

                                <div class="row">

                                    {{-- Nilai Taksiran --}}
                                    <div class="col-md-4 mb-3">

                                        <label class="form-label">
                                            Nilai Taksiran
                                        </label>

                                        <input
                                            type="text"
                                            name="jaminan[{{ $index }}][nilai_taksiran]"
                                            class="form-control rupiah"
                                            value="{{ old(
                                                "jaminan.$index.nilai_taksiran",
                                                $jaminan->nilai_taksiran
                                            ) }}"
                                        >

                                    </div>


                                    {{-- Nama Jaminan --}}
                                    <div class="col-md-4 mb-3">

                                        <label class="form-label">
                                            Nama Jaminan
                                        </label>

                                        <input
                                            type="text"
                                            name="jaminan[{{ $index }}][nama_jaminan]"
                                            class="form-control"
                                            value="{{ old(
                                                "jaminan.$index.nama_jaminan",
                                                $jaminan->nama_jaminan
                                            ) }}"
                                        >

                                    </div>


                                    {{-- Deskripsi --}}
                                    <div class="col-md-12 mb-3">

                                        <label class="form-label">
                                            Deskripsi
                                        </label>

                                        <textarea
                                            name="jaminan[{{ $index }}][detail_jaminan]"
                                            class="form-control"
                                            rows="3"
                                        >{{ old(
                                            "jaminan.$index.detail_jaminan",
                                            $jaminan->detail_jaminan
                                        ) }}</textarea>

                                    </div>

                                </div>


                                {{-- ================================================= --}}
                                {{-- DOKUMEN JAMINAN --}}
                                {{-- ================================================= --}}

                                <div class="border rounded p-3 mt-3">

                                    <h5>
                                        DOKUMEN JAMINAN
                                    </h5>

                                    <p class="text-muted small">
                                        Anda dapat mengupload beberapa file.
                                        Format yang diperbolehkan:
                                        JPG, JPEG, PNG, PDF.
                                    </p>


                                    {{-- FILE LAMA --}}
                                    @if($jaminan->dokumenJaminans->count())

                                        <div class="mb-3">

                                            <strong>
                                                File yang sudah diupload:
                                            </strong>

                                            <div class="list-group mt-2">

                                                @foreach($jaminan->dokumenJaminans as $dokumen)

                                                    <div class="list-group-item d-flex justify-content-between align-items-center">

                                                        <span>
                                                            {{ $dokumen->nama_file }}
                                                        </span>

                                                        <a
                                                            href="{{ asset('storage/' . $dokumen->file_path) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-outline-primary"
                                                        >
                                                            Lihat
                                                        </a>

                                                    </div>

                                                @endforeach

                                            </div>

                                        </div>

                                    @endif


                                    {{-- UPLOAD FILE BARU --}}
                                    <div>

                                        <label class="form-label">
                                            Upload File
                                        </label>

                                        <input
                                            type="file"
                                            name="jaminan[{{ $index }}][files][]"
                                            class="form-control"
                                            multiple
                                            accept=".jpg,.jpeg,.png,.pdf"
                                        >

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>
{{-- ========================================================= --}}
{{-- DOKUMEN TAMBAHAN PAYROLL --}}
{{-- ========================================================= --}}

<div class="card border-success mb-4">

    <div class="card-header bg-success text-white">

        <strong>
            DOKUMEN TAMBAHAN KARYAWAN / PAYROLL
        </strong>

    </div>


    <div class="card-body">

        <div class="alert alert-info">

            <strong>
                Khusus nasabah karyawan / payroll
            </strong>

            <br>

            Dokumen berikut merupakan dokumen tambahan
            dan tidak menggantikan jaminan utama.

        </div>


        <div class="row">


            {{-- ================================================= --}}
            {{-- BPJS --}}
            {{-- ================================================= --}}

            <div class="col-md-6 mb-4">

                <label class="form-label fw-bold">

                    1. Kartu Jamsostek /
                    BPJS Ketenagakerjaan

                </label>


                <input
                    type="file"
                    name="dokumen_payroll[bpjs_ketenagakerjaan][]"
                    class="form-control"
                    multiple
                    accept=".jpg,.jpeg,.png,.pdf"
                >


                <div class="form-text">

                    Dapat memilih beberapa file.
                    Maksimal 50 MB per file.

                </div>


                @php

                    $bpjsDocs =
                        $pengajuan
                            ->dokumenPayrolls
                            ->where(
                                'jenis_dokumen',
                                'bpjs_ketenagakerjaan'
                            );

                @endphp


                @if($bpjsDocs->count())

                    <div class="mt-3">

                        <strong class="small">
                            File yang sudah diupload:
                        </strong>


                        @foreach(
                            $bpjsDocs
                            as $dokumen
                        )

                            <div class="mt-2">

                                <a
                                    href="{{ asset('storage/' . $dokumen->file_path) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-primary"
                                >

                                    📄
                                    {{ $dokumen->nama_file }}

                                </a>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>


            {{-- ================================================= --}}
            {{-- BUKU TABUNGAN --}}
            {{-- ================================================= --}}

            <div class="col-md-6 mb-4">

                <label class="form-label fw-bold">

                    2. Buku Tabungan

                </label>


                <input
                    type="file"
                    name="dokumen_payroll[buku_tabungan][]"
                    class="form-control"
                    multiple
                    accept=".jpg,.jpeg,.png,.pdf"
                >


                <div class="form-text">

                    Dapat memilih beberapa file.
                    Maksimal 50 MB per file.

                </div>


                @php

                    $tabunganDocs =
                        $pengajuan
                            ->dokumenPayrolls
                            ->where(
                                'jenis_dokumen',
                                'buku_tabungan'
                            );

                @endphp


                @if($tabunganDocs->count())

                    <div class="mt-3">

                        <strong class="small">
                            File yang sudah diupload:
                        </strong>


                        @foreach(
                            $tabunganDocs
                            as $dokumen
                        )

                            <div class="mt-2">

                                <a
                                    href="{{ asset('storage/' . $dokumen->file_path) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-primary"
                                >

                                    📄
                                    {{ $dokumen->nama_file }}

                                </a>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>


            {{-- ================================================= --}}
            {{-- ATM --}}
            {{-- ================================================= --}}

            <div class="col-md-6 mb-4">

                <label class="form-label fw-bold">

                    3. Kartu ATM

                </label>


                <input
                    type="file"
                    name="dokumen_payroll[atm][]"
                    class="form-control"
                    multiple
                    accept=".jpg,.jpeg,.png,.pdf"
                >


                <div class="form-text">

                    Dapat memilih beberapa file.
                    Maksimal 50 MB per file.

                </div>


                @php

                    $atmDocs =
                        $pengajuan
                            ->dokumenPayrolls
                            ->where(
                                'jenis_dokumen',
                                'atm'
                            );

                @endphp


                @if($atmDocs->count())

                    <div class="mt-3">

                        <strong class="small">
                            File yang sudah diupload:
                        </strong>


                        @foreach(
                            $atmDocs
                            as $dokumen
                        )

                            <div class="mt-2">

                                <a
                                    href="{{ asset('storage/' . $dokumen->file_path) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-primary"
                                >

                                    📄
                                    {{ $dokumen->nama_file }}

                                </a>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>


            {{-- ================================================= --}}
            {{-- SLIP GAJI --}}
            {{-- ================================================= --}}

            <div class="col-md-6 mb-4">

                <label class="form-label fw-bold">

                    4. Slip Gaji
                    <span class="text-muted">
                        (Untuk Karyawan)
                    </span>

                </label>


                <input
                    type="file"
                    name="dokumen_payroll[slip_gaji][]"
                    class="form-control"
                    multiple
                    accept=".jpg,.jpeg,.png,.pdf"
                >


                <div class="form-text">

                    Dapat memilih beberapa file.
                    Maksimal 50 MB per file.

                </div>


                @php

                    $slipGajiDocs =
                        $pengajuan
                            ->dokumenPayrolls
                            ->where(
                                'jenis_dokumen',
                                'slip_gaji'
                            );

                @endphp


                @if($slipGajiDocs->count())

                    <div class="mt-3">

                        <strong class="small">
                            File yang sudah diupload:
                        </strong>


                        @foreach(
                            $slipGajiDocs
                            as $dokumen
                        )

                            <div class="mt-2">

                                <a
                                    href="{{ asset('storage/' . $dokumen->file_path) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-primary"
                                >

                                    📄
                                    {{ $dokumen->nama_file }}

                                </a>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>
            </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('pengajuan.analisa',$pengajuan->id) }}"class="btn btn-warning">
                            Previous
                        </a>
                        <button class="btn btn-primary">
                            Simpan & Lanjut Kapital
                        </button>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection

@section('scripts')
<script>

$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | INDEX JAMINAN
    |--------------------------------------------------------------------------
    */

    let index = {{ $jaminans->count() }};


    /*
    |--------------------------------------------------------------------------
    | TOGGLE JAMINAN UTAMA
    |--------------------------------------------------------------------------
    */

    function toggleJaminan(container)
    {

        let jenis =container.find('.jenis-jaminan').val();
        let kendaraan =container.find('.data-kendaraan');
        let tanah =container.find('.data-tanah');
        let skKerja =container.find('.data-sk-kerja');
        let nilaiUmum =container.find('.data-nilai-umum');
        /*
        |--------------------------------------------------------------------------
        | BPKB MOTOR / MOBIL
        |--------------------------------------------------------------------------
        */

        if (
            jenis === 'BPKB Motor' ||
            jenis === 'BPKB Mobil'
        ) {

            kendaraan.stop(true, true).slideDown(200);
            tanah.stop(true, true).hide();
            skKerja.stop(true, true).hide();
            nilaiUmum.stop(true, true).slideDown(200);
        }


        /*
        |--------------------------------------------------------------------------
        | SURAT TANAH
        |--------------------------------------------------------------------------
        */

        else if (
            jenis === 'Surat Tanah'
        ) {
            kendaraan.stop(true, true).hide();
            tanah.stop(true, true).slideDown(200);
            skKerja.stop(true, true).hide();
            nilaiUmum.stop(true, true).slideDown(200);

        }
        /*
        |--------------------------------------------------------------------------
        | SK KERJA
        |--------------------------------------------------------------------------
        */

        else if (
            jenis === 'SK Kerja'
        ) {
            kendaraan.stop(true, true).hide();
            tanah.stop(true, true).hide();
            skKerja.stop(true, true).slideDown(200);
            nilaiUmum.stop(true, true).hide();
        }
        /*
        |--------------------------------------------------------------------------
        | BELUM MEMILIH
        |--------------------------------------------------------------------------
        */

        else {
            kendaraan.stop(true, true).hide();
            tanah.stop(true, true).hide();
            skKerja.stop(true, true).hide();
            nilaiUmum.stop(true, true).slideDown(200);
        }

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL TOGGLE
    |--------------------------------------------------------------------------
    |
    | Saat edit data lama.
    |
    */

    $('.jaminan-item').each(function () {

        toggleJaminan(
            $(this)
        );

    });


    /*
    |--------------------------------------------------------------------------
    | CHANGE JENIS JAMINAN
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '.jenis-jaminan',
        function () {

            let container =
                $(this)
                    .closest(
                        '.jaminan-item'
                    );


            toggleJaminan(
                container
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | TAMBAH JAMINAN
    |--------------------------------------------------------------------------
    */

  $('#addJaminan').click(function () {

    let html = `

        <div
            class="card border mb-4 jaminan-item"
            data-index="${index}"
        >

            <div class="card-header bg-light d-flex justify-content-between align-items-center">

                <strong>
                    Jaminan ${index + 1}
                </strong>

                <button
                    type="button"
                    class="btn btn-danger btn-sm removeJaminan"
                >
                    Hapus Jaminan
                </button>

            </div>


            <div class="card-body">


                {{-- JENIS JAMINAN --}}

                <div class="row mb-4">

                    <div class="col-md-6">

                        <label class="form-label">
                            Jenis Jaminan
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="jaminan[${index}][jenis_jaminan]"
                            class="form-select jenis-jaminan"
                        >

                            <option value="">
                                -- Pilih Jenis Jaminan --
                            </option>

                            <option value="BPKB Motor">
                                BPKB Motor
                            </option>

                            <option value="BPKB Mobil">
                                BPKB Mobil
                            </option>

                            <option value="Surat Tanah">
                                Surat Tanah
                            </option>
                            <option value="SK Kerja">
                                SK Kerja
                            </option>
                        </select>

                    </div>

                </div>


                {{-- DATA KENDARAAN --}}

                <div
                    class="data-kendaraan"
                    style="display:none;"
                >

                    <div class="alert alert-primary">

                        <strong>
                            DATA KENDARAAN
                        </strong>

                    </div>


                    <div class="row">

                        <div class="col-md-3 mb-3">

                            <label class="form-label">
                                Jenis Kendaraan
                            </label>

                            <select
                                name="jaminan[${index}][jenis_kendaraan]"
                                class="form-select"
                            >

                                <option value="">
                                    -- Pilih --
                                </option>

                                <option value="motor">
                                    Motor
                                </option>

                                <option value="mobil">
                                    Mobil
                                </option>

                            </select>

                        </div>


                        <div class="col-md-3 mb-3">

                            <label class="form-label">
                                Tahun
                            </label>

                            <input
                                type="number"
                                name="jaminan[${index}][tahun_kendaraan]"
                                class="form-control"
                                min="1900"
                                max="{{ date('Y') + 1 }}"
                            >

                        </div>


                        <div class="col-md-3 mb-3">

                            <label class="form-label">
                                Merk
                            </label>

                            <input
                                type="text"
                                name="jaminan[${index}][merk_kendaraan]"
                                class="form-control"
                            >

                        </div>


                        <div class="col-md-3 mb-3">

                            <label class="form-label">
                                Plat Polisi
                            </label>

                            <input
                                type="text"
                                name="jaminan[${index}][plat_polisi]"
                                class="form-control"
                            >

                        </div>


                        {{-- BPKB --}}

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                BPKB
                            </label>

                            <div>

                                <label class="me-3">

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][bpkb_status]"
                                        value="ada"
                                    >

                                    Ada

                                </label>

                                <label>

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][bpkb_status]"
                                        value="tidak_ada"
                                    >

                                    Tidak Ada

                                </label>

                            </div>

                        </div>


                        {{-- PAJAK STNK --}}

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Pajak STNK
                            </label>

                            <div>

                                <label class="me-3">

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][pajak_stnk_status]"
                                        value="ada"
                                    >

                                    Ada

                                </label>

                                <label>

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][pajak_stnk_status]"
                                        value="tidak_ada"
                                    >

                                    Tidak Ada

                                </label>

                            </div>

                        </div>


                        {{-- STATUS PAJAK --}}

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Status Pajak
                            </label>

                            <div>

                                <label class="me-3">

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][status_pajak]"
                                        value="hidup"
                                    >

                                    Hidup

                                </label>

                                <label>

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][status_pajak]"
                                        value="mati"
                                    >

                                    Mati

                                </label>

                            </div>

                        </div>


                        <div class="col-md-3 mb-3">

                            <label class="form-label">
                                BPKB Atas Nama
                            </label>

                            <input
                                type="text"
                                name="jaminan[${index}][bpkb_atas_nama]"
                                class="form-control"
                            >

                        </div>


                        <div class="col-md-3 mb-3">

                            <label class="form-label">
                                No BPKB
                            </label>

                            <input
                                type="text"
                                name="jaminan[${index}][no_bpkb]"
                                class="form-control"
                            >

                        </div>


                        <div class="col-md-3 mb-3">

                            <label class="form-label">
                                No Rangka
                            </label>

                            <input
                                type="text"
                                name="jaminan[${index}][no_rangka]"
                                class="form-control"
                            >

                        </div>


                        <div class="col-md-3 mb-3">

                            <label class="form-label">
                                No Mesin
                            </label>

                            <input
                                type="text"
                                name="jaminan[${index}][no_mesin]"
                                class="form-control"
                            >

                        </div>

                    </div>

                </div>


                {{-- DATA SURAT TANAH --}}

                <div class="data-tanah"style="display:none;">

                    <div class="alert alert-warning">

                        <strong>
                            DATA SURAT TANAH
                        </strong>

                    </div>


                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                SKT/SPGR
                            </label>

                            <div>

                                <label class="me-3">

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][skt_spgr_status]"
                                        value="ada"
                                    >

                                    Ada

                                </label>

                                <label>

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][skt_spgr_status]"
                                        value="tidak_ada"
                                    >

                                    Tidak Ada

                                </label>

                            </div>

                        </div>


                        <div class="col-md-8 mb-3">

                            <label class="form-label">
                                SKT/SPGR Dikeluarkan Oleh
                            </label>

                            <div>

                                <label class="me-3">

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][skt_spgr_dikeluarkan_oleh]"
                                        value="camat"
                                    >

                                    Camat

                                </label>

                                <label class="me-3">

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][skt_spgr_dikeluarkan_oleh]"
                                        value="kepala_desa"
                                    >

                                    Kepala Desa

                                </label>

                                <label class="me-3">

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][skt_spgr_dikeluarkan_oleh]"
                                        value="kepala_dusun"
                                    >

                                    Kepala Dusun

                                </label>

                                <label>

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][skt_spgr_dikeluarkan_oleh]"
                                        value="bawah_tangan"
                                    >

                                    Bawah Tangan

                                </label>

                            </div>

                        </div>


                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Sertifikat
                            </label>

                            <div>

                                <label class="me-3">

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][sertifikat_status]"
                                        value="ada"
                                    >

                                    Ada

                                </label>

                                <label>

                                    <input
                                        type="radio"
                                        name="jaminan[${index}][sertifikat_status]"
                                        value="tidak_ada"
                                    >

                                    Tidak Ada

                                </label>

                            </div>

                        </div>

                    </div>

                </div>

{{-- DATA SK KERJA --}}

<div class="data-sk-kerja" style="display:none;">

    <div class="alert alert-success">

        <strong>
            DATA SK KERJA
        </strong>

    </div>

    <div class="row">

        <div class="col-md-6 mb-3">

            <label class="form-label">

                Nomor SK Kerja

                <span class="text-danger">*</span>

            </label>

            <input
                type="text"
                name="jaminan[${index}][no_sk_kerja]"
                class="form-control"
                placeholder="Masukkan nomor SK Kerja"
            >

        </div>

        <div class="col-md-6 mb-3">

            <div class="alert alert-info mb-0">

                <strong>
                    Dokumen SK Kerja
                </strong>

                <br>

                JPG, JPEG, PNG atau PDF.

            </div>

        </div>

    </div>

</div>
                {{-- NILAI DAN DESKRIPSI --}}
            <div class="data-nilai-umum">
                <hr>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            Nilai Taksiran
                        </label>

                        <input
                            type="text"
                            name="jaminan[${index}][nilai_taksiran]"
                            class="form-control rupiah"
                        >

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Nama Jaminan
                        </label>

                        <input
                            type="text"
                            name="jaminan[${index}][nama_jaminan]"
                            class="form-control"
                        >

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Deskripsi
                        </label>

                        <textarea
                            name="jaminan[${index}][detail_jaminan]"
                            class="form-control"
                            rows="3"
                        ></textarea>

                    </div>

                </div>
            </div>


                {{-- DOKUMEN JAMINAN --}}

                <div class="border rounded p-3 mt-3">

                    <h5>
                        DOKUMEN JAMINAN
                    </h5>

                    <p class="text-muted small">

                        Upload satu atau beberapa file.
                        Format JPG, JPEG, PNG atau PDF.

                    </p>


                    <input
                        type="file"
                        name="jaminan[${index}][files][]"
                        class="form-control"
                        multiple
                        accept=".jpg,.jpeg,.png,.pdf"
                    >

                </div>

            </div>

        `;


    $('#jaminan_wrapper')
        .append(html);


    let newContainer =
        $('#jaminan_wrapper')
            .find('.jaminan-item')
            .last();


    toggleJaminan(
        newContainer
    );


    index++;

});


    /*
    |--------------------------------------------------------------------------
    | HAPUS JAMINAN
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.removeJaminan',
        function () {

            $(this)
                .closest('.jaminan-item')
                .remove();

        }
    );

});

</script>
@endsection