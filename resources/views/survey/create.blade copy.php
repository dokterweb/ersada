@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Create Survey
            </h2>
          </div>
          
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-blue-lt">
                    <h3 class="card-title">Data Pengajuan Pembiayaan</h3>
                </div>
                <div class="card-body">
                
                    <div class="row">
                        <div class="col-md-4">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="220">No Pengajuan</th>
                                    <td>{{ $pengajuan->nomor_pengajuan }}</td>
                                </tr>
                                <tr>
                                    <th>Nasabah</th>
                                    <td>{{ optional($pengajuan->nasabah)->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Marketing</th>
                                    <td>{{ optional($pengajuan->marketing->user)->name }}</td>
                                </tr>
                                <tr>
                                    <th>Cabang</th>
                                    <td>{{ optional($pengajuan->cabang)->nama_cabang }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <table class="table table-bordered">
                                <tr>
                                    <td>Tanggal Pengajuan</td>
                                    <td>{{ $pengajuan->tanggal_pengajuan }}</td>
                                </tr>
                                <tr>
                                    <td>Nominal</td>
                                    <td>{{ number_format($pengajuan->nominal_pengajuan) }}</td>
                                </tr>
                                <tr>
                                    <td>Tenor</td>
                                    <td>{{ $pengajuan->tenor }} Bulan</td>
                                </tr>
                                <tr>
                                    <td>Pekerjaan</td>
                                    <td>{{ $pengajuan->kategori_nasabah }}</td>
                                </tr>
                                <tr>
                                    <td>Tujuan Pinjaman</td>
                                    <td>{{ $pengajuan->tujuan_pinjaman }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4 text-center">

                                <strong>Foto Nasabah</strong>

                                <div class="mt-2">

                                    @if($pengajuan->nasabah?->foto_nasabah)

                                        <img
                                            src="{{ asset('storage/' . $pengajuan->nasabah->foto_nasabah) }}"
                                            class="img-thumbnail"
                                            style="max-width:180px; max-height:220px;"
                                        >

                                    @else

                                        <div class="text-muted">
                                            Foto belum tersedia
                                        </div>

                                    @endif

                                </div>

                            </div>                        
                    </div>
                    {{-- DATA NASABAH --}}
                    <div class="card mb-4">

                        <div class="card-header bg-success text-white">
                            <h3 class="card-title mb-0">
                                Data Nasabah
                            </h3>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-4 mb-3">
                                    <strong>Nama</strong>
                                    <div>
                                        {{ $pengajuan->nasabah?->nama ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <strong>NIK</strong>
                                    <div>
                                        {{ $pengajuan->nasabah?->nik ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <strong>No. HP</strong>
                                    <div>
                                        {{ $pengajuan->nasabah?->no_hp ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <strong>Tempat / Tanggal Lahir</strong>
                                    <div>
                                        {{ $pengajuan->nasabah?->tempat_lahir ?? '-' }}
                                        /
                                        {{ $pengajuan->nasabah?->tgl_lahir ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <strong>Status Perkawinan</strong>
                                    <div>
                                        {{ $pengajuan->nasabah?->status_perkawinan ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <strong>Jumlah Tanggungan</strong>
                                    <div>
                                        {{ $pengajuan->nasabah?->jumlah_tanggungan ?? 0 }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <strong>Status Rumah</strong>
                                    <div>
                                        {{ $pengajuan->nasabah?->status_rumah ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-8 mb-3">
                                    <strong>Alamat</strong>
                                    <div>
                                        {{ $pengajuan->nasabah?->alamat ?? '-' }}
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                    {{-- DATA PEKERJAAN --}}
                    @php
                        $pekerjaan = $pengajuan->nasabah?->pekerjaanNasabah;
                    @endphp
                    <div class="card mb-4">

                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title mb-0">
                                Data Pekerjaan
                            </h3>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-4 mb-3">
                                    <strong>Jenis Pekerjaan</strong>
                                    <div>
                                        {{ $pekerjaan?->jenis_pekerjaan ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <strong>Penghasilan</strong>
                                    <div>
                                        Rp {{ number_format($pekerjaan?->penghasilan ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <strong>Nama Usaha</strong>
                                    <div>
                                        {{ $pekerjaan?->nama_usaha ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <strong>Jenis Usaha</strong>
                                    <div>
                                        {{ $pekerjaan?->jenis_usaha ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <strong>Lama Usaha</strong>
                                    <div>
                                        {{ $pekerjaan?->lama_usaha ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <strong>Jumlah Pegawai</strong>
                                    <div>
                                        {{ $pekerjaan?->jumlah_pegawai ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <strong>Alamat Usaha</strong>
                                    <div>
                                        {{ $pekerjaan?->alamat_usaha ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <strong>Status Tempat Usaha</strong>
                                    <div>
                                        {{ $pekerjaan?->status_tempat_usaha ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <strong>Aktivitas Usaha</strong>
                                    <div>
                                        {{ $pekerjaan?->aktivitas_usaha ?? '-' }}
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- DATA PASANGAN --}}
                    @if($pasangan)
                    <div class="card mb-4">

                        <div class="card-header bg-warning">
                            <h3 class="card-title mb-0">
                                Data Pasangan
                            </h3>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-4">
                                    <strong>Nama</strong>
                                    <div>{{ $pasangan->nama }}</div>
                                </div>

                                <div class="col-md-4">
                                    <strong>Tempat Lahir</strong>
                                    <div>{{ $pasangan->tempat_lahir ?? '-' }}</div>
                                </div>

                                <div class="col-md-4">
                                    <strong>Tanggal Lahir</strong>
                                    <div>{{ $pasangan->tgl_lahir ?? '-' }}</div>
                                </div>

                            </div>

                        </div>

                    </div>
                    @endif

                    {{-- DATA PENJAMIN --}}
                    @if($penjamin)
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title mb-0">
                                Data Penjamin
                            </h3>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-4">
                                    <strong>Nama</strong>
                                    <div>{{ $penjamin->nama }}</div>
                                </div>

                                <div class="col-md-4">
                                    <strong>Hubungan</strong>
                                    <div>{{ $penjamin->hubungan ?? '-' }}</div>
                                </div>

                                <div class="col-md-4">
                                    <strong>No. HP</strong>
                                    <div>{{ $penjamin->no_hp ?? '-' }}</div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <strong>Alamat</strong>
                                    <div>{{ $penjamin->alamat ?? '-' }}</div>
                                </div>

                            </div>

                        </div>

                    </div>
                    @endif

                    {{-- DATA SAUDARA --}}
                    @if($saudaras->count())
                    <div class="card mb-4">

                        <div class="card-header bg-secondary text-white">
                            <h3 class="card-title mb-0">
                                Data Saudara
                            </h3>
                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-bordered">

                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Hubungan</th>
                                            <th>No. HP</th>
                                            <th>Alamat</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach($saudaras as $index => $saudara)

                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $saudara->nama }}</td>
                                            <td>{{ $saudara->hubungan ?? '-' }}</td>
                                            <td>{{ $saudara->no_hp ?? '-' }}</td>
                                            <td>{{ $saudara->alamat ?? '-' }}</td>
                                        </tr>

                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>
                    @endif

                    {{-- DOKUMEN --}}
                    <div class="card mb-4">

                        <div class="card-header bg-success text-white">
                            <h3 class="card-title mb-0">
                                Dokumen Pengajuan
                            </h3>
                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-bordered">

                                    <thead>
                                        <tr>
                                            <th>Jenis Dokumen</th>
                                            <th>Nama File</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse($pengajuan->dokumenPengajuans as $dokumen)

                                        <tr>

                                            <td>
                                                {{ ucwords(str_replace('_', ' ', $dokumen->jenis_dokumen)) }}
                                            </td>

                                            <td>
                                                {{ $dokumen->nama_file }}
                                            </td>

                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $dokumen->status }}
                                                </span>
                                            </td>

                                            <td>

                                                <a
                                                    href="{{ asset('storage/' . $dokumen->file_path) }}"
                                                    target="_blank"
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    Lihat
                                                </a>

                                            </td>

                                        </tr>

                                        @empty

                                        <tr>
                                            <td colspan="4" class="text-center">
                                                Belum ada dokumen.
                                            </td>
                                        </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                    {{-- ANALISA --}}
                    @if($pengajuan->analisa)
                    <div class="card mb-4">

                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title mb-0">
                                Analisa Pengajuan
                            </h3>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                {{-- Sesuaikan field dengan tabel analisa_pengajuans --}}

                                <div class="col-md-6 mb-3">
                                    <strong>Hasil Analisa</strong>
                                    <div>
                                        {{ $pengajuan->analisa->hasil ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <strong>Catatan</strong>
                                    <div>
                                        {{ $pengajuan->analisa->catatan ?? '-' }}
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                    @endif

                    {{-- KAPITAL --}}
                    @if($pengajuan->kapital)

                    <div class="card mb-4">

                        <div class="card-header bg-info text-white">
                            <h3 class="card-title mb-0">
                                Analisa Kapital
                            </h3>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-6">

                                    <h5 class="text-primary">
                                        Pendapatan
                                    </h5>

                                    <table class="table table-sm">

                                        <tr>
                                            <td>Omzet Harian</td>
                                            <td class="text-end">
                                                Rp {{ number_format($pengajuan->kapital->omzet_harian ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Laba Harian</td>
                                            <td class="text-end">
                                                Rp {{ number_format($pengajuan->kapital->laba_harian ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Gaji Debitur</td>
                                            <td class="text-end">
                                                Rp {{ number_format($pengajuan->kapital->gaji_debitur ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Pendapatan Pasangan</td>
                                            <td class="text-end">
                                                Rp {{ number_format($pengajuan->kapital->pendapatan_pasangan ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>

                                    </table>

                                </div>

                                <div class="col-md-6">

                                    <h5 class="text-danger">
                                        Pengeluaran
                                    </h5>

                                    <table class="table table-sm">

                                        <tr>
                                            <td>Rumah Tangga</td>
                                            <td class="text-end">
                                                Rp {{ number_format($pengajuan->kapital->biaya_rumah_tangga ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Motor</td>
                                            <td class="text-end">
                                                Rp {{ number_format($pengajuan->kapital->biaya_motor ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Koperasi</td>
                                            <td class="text-end">
                                                Rp {{ number_format($pengajuan->kapital->biaya_koperasi ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Angsuran Lain</td>
                                            <td class="text-end">
                                                Rp {{ number_format($pengajuan->kapital->angsuran_lain ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Kontrak Rumah</td>
                                            <td class="text-end">
                                                Rp {{ number_format($pengajuan->kapital->biaya_kontrak_rumah ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Tempat Usaha</td>
                                            <td class="text-end">
                                                Rp {{ number_format($pengajuan->kapital->biaya_tempat_usaha ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>

                                    </table>

                                </div>

                            </div>

                            <hr>

                            <div class="row">

                                <div class="col-md-6">

                                    <strong>Total Pengeluaran</strong>

                                    <h3 class="text-danger">
                                        Rp {{ number_format($pengajuan->kapital->total_pengeluaran ?? 0, 0, ',', '.') }}
                                    </h3>

                                </div>

                                <div class="col-md-6 text-end">

                                    <strong>Sisa Pendapatan</strong>

                                    <h3 class="text-success">
                                        Rp {{ number_format($pengajuan->kapital->sisa_pendapatan ?? 0, 0, ',', '.') }}
                                    </h3>

                                </div>

                            </div>

                        </div>

                    </div>
                    @endif

                    <hr>
                <form action="{{ route('survey.store',$pengajuan->id) }}" method="POST">
                @csrf
                <div class="card-body">                    
                    <div class="mb-3">
                        <label class="form-label">Jenis Penugasan</label>
                        <select name="jenis" id="jenis" class="form-select">
                            @if($canSurveySelf)
                                <option value="sendiri"
                                    {{ old('jenis', 'sendiri') == 'sendiri' ? 'selected' : '' }}>
                                    Survey Saya Sendiri
                                </option>
                            @endif
                            <option value="assign"
                                {{ old('jenis') == 'assign' ? 'selected' : '' }}>
                                Tugaskan Surveyor
                            </option>
                        </select>
                        @error('jenis')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3" id="surveyor-area">
                        <label class="form-label">Pilih Surveyor</label>
                        <select name="surveyor_id" id="surveyor_id" class="form-select">
                            <option value="">-- Pilih Surveyor --</option>
                            @foreach($surveyors as $surveyor)
                                <option value="{{ $surveyor->id }}" {{ old('surveyor_id') == $surveyor->id ? 'selected' : '' }}>
                                    {{ $surveyor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('surveyor_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('survey.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                    <button class="btn btn-success">Simpan Penugasan</button>
                </div>
                </form>
            
            </div>
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection

@section('scripts')

<script>
    $(function () {

        function toggleSurveyor() {

            let jenis = $('#jenis').val();

            if (jenis === 'assign') {

                $('#surveyor-area').slideDown(200);

                $('#surveyor_id')
                    .prop('required', true);

            } else {

                $('#surveyor-area').slideUp(200);

                $('#surveyor_id')
                    .prop('required', false)
                    .val('');

            }
        }

        // Jalankan saat halaman pertama kali dibuka
        toggleSurveyor();

        // Jalankan ketika pilihan berubah
        $('#jenis').on('change', function () {

            toggleSurveyor();

        });

    });
</script>

@endsection