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
                <div class="card">
                    <div class="card-header bg-blue-lt">
                        <h3 class="card-title">Pelunasan Pembiayaan</h3>
                    </div>
                    <form method="POST" action="{{ route('pelunasan.store',$pembiayaan) }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>No Pembiayaan</th>
                                        <td>{{ $pembiayaan->nomor_pembiayaan }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Nasabah</th>
                                        <td>{{ $pembiayaan->pengajuan->nasabah->nama }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Pelunasan</th>
                                        <td><input type="date" name="tanggal_pelunasan" class="form-control" value="{{ date('Y-m-d') }}"></td>
                                    </tr>
                                    <tr>
                                        <th>Sisa Pokok</th>
                                        <td>
                                            <input readonly type="text" id="sisa_pokok_view" class="form-control text-end" value="{{ number_format($sisaPokok,0,',','.') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Sisa Bunga</th>
                                        <td>
                                            <input readonly type="text" class="form-control text-end" value="{{ number_format($sisaBunga,0,',','.') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Denda</th>
                                        <td>
                                            <input readonly type="text" class="form-control text-end" value="{{ number_format($denda,0,',','.') }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Diskon</th>
                                        <td><input type="number" name="diskon" id="diskon" class="form-control text-end" value="0"></td>
                                    </tr>
                                    <tr class="table-success">
                                        <th>Total Pelunasan</th>
                                        <td>
                                            <input readonly type="text" id="total_view" class="form-control fw-bold text-end">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <label>Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="8"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-danger"><i class="fas fa-save"></i>Simpan Pelunasan</button>
                        <a href="{{ route('operasional.show',$pembiayaan) }}" class="btn btn-secondary">Kembali</a>
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
let pokok={{ $sisaPokok }};
let bunga={{ $sisaBunga }};
let denda={{ $denda }};

function hitung(){
    let diskon=parseInt($("#diskon").val())||0;
    let total=(pokok+bunga+denda)-diskon;
    $("#total").val(total);
    $("#total_view").val(
    new Intl.NumberFormat('id-ID').format(total)
    );
}

$("#diskon").keyup(hitung);
$("#diskon").change(hitung);

hitung();
</script>
@endsection