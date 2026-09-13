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
                            <h3 class="card-title">Preview Jadwal Angsuran</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Ke</th>
                                            <th>Jatuh Tempo</th>
                                            <th>Pokok</th>
                                            <th>Bunga</th>
                                            <th>Total</th>
                                            <th>Sisa Pokok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($jadwal as $row)
                                        <tr>
                                            <td>{{ $row['angsuran_ke'] }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row['tanggal_jatuh_tempo'])->format('d-m-Y') }}</td>
                                            <td>Rp {{ number_format($row['pokok_angsuran'],0,',','.') }}</td>
                                            <td>Rp {{ number_format($row['bunga_angsuran'],0,',','.') }}</td>
                                            <td>Rp {{ number_format($row['total_angsuran'],0,',','.') }}</td>
                                            <td>Rp {{ number_format($row['sisa_pokok'],0,',','.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                             <div class="card-footer text-end">
                                <form action="{{ route('pembiayaan.storeJadwal',$pembiayaan) }}" method="POST">
                                    @csrf
                                    <a href="{{ route('pembiayaan.review',$pembiayaan) }}" class="btn btn-secondary">Kembali</a>
                                    <button class="btn btn-success">Simpan Jadwal Angsuran</button>
                                </form>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection