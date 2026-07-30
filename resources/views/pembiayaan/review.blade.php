@extends('layouts.app')
@section('title','Review Pembiayaan')
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <strong>DATA DEBITUR</strong>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="180">Nomor Pengajuan</th>
                                            <td>{{ $pembiayaan->pengajuan->nomor_pengajuan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nomor Pembiayaan</th>
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
                                    </table>
                                </div>
                            </div>
                        </div>
                
                        {{-- CARD 2 PEMBIAYAAN --}}
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-success text-white">
                                    <strong>DATA PEMBIAYAAN</strong>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="180">Plafond</th>
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
                                            <th>Bunga</th>
                                            <td>{{ $pembiayaan->persen_bunga }} %</td>
                                        </tr>
                                        <tr>
                                            <th>Jatuh Tempo Pertama</th>
                                            <td>{{ $pembiayaan->tanggal_jatuh_tempo_pertama->format('d-m-Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                
                        {{-- CARD 3 BIAYA --}}
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-warning">
                                    <strong>BIAYA</strong>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="180">Administrasi</th>
                                            <td>Rp {{ number_format($pembiayaan->biaya_administrasi,0,',','.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Materai</th>
                                            <td>Rp {{ number_format($pembiayaan->materai,0,',','.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Biaya Survey</th>
                                            <td>Rp {{ number_format($pembiayaan->biaya_survei,0,',','.') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{-- CARD 4 DANA DITERIMA --}}
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-info text-white">
                                    <strong>DANA DITERIMA NASABAH</strong>
                                </div>
                                <div class="card-body text-center">
                                    <h2 class="text-success">
                                        Rp {{ number_format($pembiayaan->dana_diterima,0,',','.') }}
                                    </h2>
                                </div>
                            </div>
                        </div>

                        {{-- CARD 5 SIMULASI --}}
                        <div class="col-md-12">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>SIMULASI PEMBAYARAN</strong>
                                </div>
                                <div class="card-body">
                                    @if($pembiayaan->jenis_tenor=='pendek')
                                        @php
                                            $bunga = $pembiayaan->plafond * ($pembiayaan->persen_bunga/100);
                                        @endphp
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Bulan</th>
                                                    <th>Keterangan</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for($i=1;$i<=$pembiayaan->tenor;$i++)
                                                    <tr>
                                                        <td>{{ $i }}</td>
                                                        @if($i==$pembiayaan->tenor)
                                                            <td>Pelunasan Pokok + Bunga</td>
                                                            <td>Rp {{ number_format($pembiayaan->plafond+$bunga,0,',','.') }}</td>
                                                        @else
                                                            <td>Bunga</td>
                                                            <td>Rp {{ number_format($bunga,0,',','.') }}</td>
                                                        @endif
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    @else
                                        @php
                                            $pokok = $pembiayaan->plafond/$pembiayaan->tenor;
                                            $bunga = $pembiayaan->plafond*($pembiayaan->persen_bunga/100);
                                            $total = $pokok+$bunga;
                                        @endphp
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="250">Pokok / Bulan</th>
                                                <td>Rp {{ number_format($pokok,0,',','.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Bunga / Bulan</th>
                                                <td>Rp {{ number_format($bunga,0,',','.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total Angsuran</th>
                                                <td><strong>Rp {{ number_format($total,0,',','.') }}</strong></td>
                                            </tr>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- CARD 6 KONFIRMASI --}}
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-danger text-white">
                                    <strong>KONFIRMASI</strong>
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="confirm" value="1" required>
                                        <label class="form-check-label">
                                            Saya telah memeriksa seluruh data pembiayaan dan siap
                                            membuat Jadwal Angsuran.
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('pembiayaan.edit',$pembiayaan) }}"
                            class="btn btn-warning">
                            Edit Pembiayaan
                        </a>
                        <a href="{{ route('pembiayaan.generateJadwal',$pembiayaan) }}"class="btn btn-success">
                            Generate Jadwal Angsuran
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
