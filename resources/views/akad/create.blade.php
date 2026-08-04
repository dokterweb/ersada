@extends('layouts.app')
@section('title','Approval Survey')
@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                Create Akad
                </h2>
            </div>
        
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <form action="{{ route('akad.store',$pembiayaan) }}" method="POST">
            @csrf
                <div class="row row-cards">
                    <div class="col-lg-6">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <strong>DATA PEMBIAYAAN</strong>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="170">No Pengajuan</th>
                                        <td>{{ $pembiayaan->pengajuan->nomor_pengajuan }}</td>
                                    </tr>
                                    <tr>
                                        <th>No Pembiayaan</th>
                                        <td>{{ $pembiayaan->nomor_pembiayaan }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Debitur</th>
                                        <td>{{ optional($pembiayaan->pengajuan->nasabah)->nama }}</td>
                                    </tr>
                                    <tr>
                                        <th>Marketing</th>
                                        <td>{{ optional($pembiayaan->pengajuan->marketing)->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Cabang</th>
                                        <td>{{ optional($pembiayaan->pengajuan->cabang)->nama_cabang }}</td>
                                    </tr>
                                    <tr>
                                        <th>Plafond</th>
                                        <td>Rp {{ number_format($pembiayaan->plafond,0,',','.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tenor</th>
                                        <td>{{ $pembiayaan->tenor }} Bulan</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Tenor</th>
                                        <td>{{ ucfirst($pembiayaan->jenis_tenor) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dana Diterima</th>
                                        <td>Rp {{ number_format($pembiayaan->dana_diterima,0,',','.') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <strong>DATA AKAD</strong>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Akad</label>
                                    <input type="text" name="nomor_akad" class="form-control" value="{{ $nomorAkad }}"readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Akad</label>
                                    <input type="date" name="tanggal_akad" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tempat Akad</label>
                                    <input type="text" name="tempat_akad" class="form-control" value="{{ old('tempat_akad') }}"
                                            placeholder="Contoh : Kantor PT. Ersada Mandiri Jaya">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nomor Perjanjian</label>
                                    <input type="text" name="nomor_perjanjian" class="form-control" value="{{ old('nomor_perjanjian') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Catatan</label>
                                    <textarea name="catatan" rows="5"class="form-control">{{ old('catatan') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-warning">
                                <strong>DOKUMEN YANG AKAN DIGENERATE</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-check">
                                            <input type="checkbox" checked disabled class="form-check-input">
                                            <span class="form-check-label">Perjanjian Kredit</span>
                                        </label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-check">
                                            <input type="checkbox" checked disabled class="form-check-input">
                                            <span class="form-check-label">Jadwal Angsuran</span>
                                        </label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-check">
                                            <input type="checkbox" checked disabled class="form-check-input">
                                            <span class="form-check-label">Kwitansi</span>
                                        </label>
                                    </div>
                                   {{--  <div class="col-md-3">
                                        <label class="form-check">
                                            <input type="checkbox" checked disabled class="form-check-input">
                                            <span class="form-check-label">Surat Kuasa</span>
                                        </label>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('pembiayaan.jadwal',$pembiayaan) }}"
                        class="btn btn-secondary">
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-success">
                        Simpan Akad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>

</script>
@endsection