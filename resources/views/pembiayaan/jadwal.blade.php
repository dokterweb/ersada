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
   
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">Jadwal Angsuran</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <table class="table table-sm table-border">
                                <tr>
                                    <th width="180">No. Pengajuan</th>
                                    <td>{{ $pembiayaan->pengajuan->nomor_pengajuan }}</td>
                                </tr>
                                <tr>
                                    <th>No. Pembiayaan</th>
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
                        <div class="col-md-4">
                            <table class="table table-sm table-border">
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
                                    <th>Dana Diterima</th>
                                    <td>
                                        Rp {{ number_format($pembiayaan->dana_diterima,0,',','.') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <table class="table table-sm table-border">
                                <tr>
                                    <th width="180">Total Angsuran</th>
                                    <td>{{ $ringkasan['total_angsuran'] }} Kali</td>
                                </tr>
                                <tr>
                                    <th>Total Pokok</th>
                                    <td>Rp {{ number_format($ringkasan['total_pokok'],0,',','.') }}</td>
                                </tr>
                                <tr>
                                    <th>Total Bunga</th>
                                    <td> Rp {{ number_format($ringkasan['total_bunga'],0,',','.') }}</td>
                                </tr>
                                <tr>
                                    <th>Total Pembayaran</th>
                                    <td>Rp {{ number_format($ringkasan['total_pembayaran'],0,',','.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Pokok</th>
                                    <th>Bunga</th>
                                    <th>Total</th>
                                    <th>Sisa Pokok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($pembiayaan->angsurans as $angsuran)
                                <tr>
                                    <td>{{ $angsuran->angsuran_ke }}</td>
                                    <td>{{ $angsuran->tanggal_jatuh_tempo->format('d-m-Y') }}</td>
                                    <td class="text-end">{{ number_format($angsuran->pokok_angsuran,0,',','.') }}</td>
                                    <td class="text-end">{{ number_format($angsuran->bunga_angsuran,0,',','.') }}</td>
                                    <td class="text-end"><strong>{{ number_format($angsuran->total_angsuran,0,',','.') }}</strong></td>
                                    <td class="text-end">{{ number_format($angsuran->sisa_pokok,0,',','.') }}</td>
                                    <td>
                                        @if($angsuran->status=='belum_jatuh_tempo')
                                            <span class="badge bg-secondary">Belum Jatuh Tempo</span>
                                        @elseif($angsuran->status=='jatuh_tempo')
                                            <span class="badge bg-warning">Jatuh Tempo</span>
                                        @else
                                            <span class="badge bg-success">Dibayar</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between">
                        <a href="{{ route('pembiayaan.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>
                        <a href="{{ route('akad.create',$pembiayaan) }}" class="btn btn-success">
                            Lanjut Proses Akad
                        </a>
                    </div>
                </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection