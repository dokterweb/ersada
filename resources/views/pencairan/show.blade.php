@extends('layouts.app')
@section('title', 'Data Pencairan')
@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Detail Pencairan
            </h2>
          </div>
         
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
            <div class="card">

                <div class="card-header">
        
                    <h3 class="card-title">
        
                        Detail Pencairan
        
                    </h3>
        
                </div>
        
                <div class="card-body">
        
                    <div class="row">
        
                        <div class="col-md-6">
        
                            <table class="table table-bordered">
        
                                <tr>
        
                                    <th width="40%">Nomor Pencairan</th>
        
                                    <td>{{ $pencairan->nomor_pencairan }}</td>
        
                                </tr>
        
                                <tr>
        
                                    <th>Tanggal</th>
        
                                    <td>
        
                                        {{ $pencairan->tanggal_pencairan->format('d-m-Y') }}
        
                                    </td>
        
                                </tr>
        
                                <tr>
        
                                    <th>Metode</th>
        
                                    <td>
        
                                        {{ ucfirst($pencairan->metode) }}
        
                                    </td>
        
                                </tr>
        
                            </table>
        
                        </div>
        
                    </div>
        
                    <hr>
        
                    <h3>Data Debitur</h3>
        
                    <table class="table table-bordered">
        
                        <tr>
        
                            <th width="25%">Nama</th>
        
                            <td>
        
                                {{ $pencairan->akad->pembiayaan->pengajuan->nasabah->nama }}
        
                            </td>
        
                        </tr>
        
                        <tr>
        
                            <th>NIK</th>
        
                            <td>
        
                                {{ $pencairan->akad->pembiayaan->pengajuan->nasabah->nik }}
        
                            </td>
        
                        </tr>
        
                        <tr>
        
                            <th>Marketing</th>
        
                            <td>
        
                                {{ $pencairan->akad->pembiayaan->pengajuan->marketing->nama }}
        
                            </td>
        
                        </tr>
        
                        <tr>
        
                            <th>Cabang</th>
        
                            <td>
        
                                {{ $pencairan->akad->pembiayaan->pengajuan->cabang->nama }}
        
                            </td>
        
                        </tr>
        
                    </table>
        
                    <hr>
        
                    <h3>Rincian Pembiayaan</h3>
        
                    <table class="table table-bordered">
        
                        <tr>
        
                            <th width="35%">Plafond</th>
        
                            <td>
        
                                Rp {{ number_format($pencairan->akad->pembiayaan->plafond,0,',','.') }}
        
                            </td>
        
                        </tr>
        
                        <tr>
        
                            <th>Administrasi</th>
        
                            <td>
        
                                Rp {{ number_format($pencairan->akad->pembiayaan->biaya_administrasi,0,',','.') }}
        
                            </td>
        
                        </tr>
        
                        <tr>
        
                            <th>Materai</th>
        
                            <td>
        
                                Rp {{ number_format($pencairan->akad->pembiayaan->materai,0,',','.') }}
        
                            </td>
        
                        </tr>
        
                        <tr>
        
                            <th>Biaya Survey</th>
        
                            <td>
        
                                Rp {{ number_format($pencairan->akad->pembiayaan->biaya_survey,0,',','.') }}
        
                            </td>
        
                        </tr>
        
                        <tr class="table-success">
        
                            <th>Dana Diterima</th>
        
                            <th>
        
                                Rp {{ number_format($pencairan->jumlah_dicairkan,0,',','.') }}
        
                            </th>
        
                        </tr>
        
                    </table>
        
                    <hr>
        
                    <h3>Data Transfer</h3>
        
                    <table class="table table-bordered">
        
                        <tr>
        
                            <th width="35%">Bank</th>
        
                            <td>{{ $pencairan->bank ?? '-' }}</td>
        
                        </tr>
        
                        <tr>
        
                            <th>No Rekening</th>
        
                            <td>{{ $pencairan->no_rekening ?? '-' }}</td>
        
                        </tr>
        
                        <tr>
        
                            <th>Atas Nama</th>
        
                            <td>{{ $pencairan->atas_nama ?? '-' }}</td>
        
                        </tr>
        
                        <tr>
        
                            <th>Keterangan</th>
        
                            <td>{{ $pencairan->keterangan ?? '-' }}</td>
        
                        </tr>
        
                    </table>
        
                </div>
        
                <div class="card-footer text-end">
        
                    <a href="{{ route('pencairan.index') }}"
                       class="btn btn-secondary">
        
                        Kembali
        
                    </a>
        
                    <button class="btn btn-primary" disabled>
        
                        Bukti Pengeluaran Kas
        
                    </button>
        
                    <button class="btn btn-success" disabled>
        
                        Kwitansi
        
                    </button>
        
                </div>
        
            </div>         
        </div>
      </div>
    </div>
    
</div>
@endsection
