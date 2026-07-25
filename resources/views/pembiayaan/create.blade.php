@extends('layouts.app')
@section('title','Tambah Pembiayaan')
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
                    <h3 class="card-title">Data Pembiayaan</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('pembiayaan.store',$pengajuan->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            {{-- ================= DATA DEBITUR ================= --}}
                            <div class="col-lg-12">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            Data Debitur
                                        </h3>
                                    </div>
                        
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label>Nomor Pengajuan</label>
                                                    <input type="text" class="form-control" value="{{ $pengajuan->nomor_pengajuan }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label>Nama Debitur</label>
                                                    <input type="text" class="form-control" value="{{ optional($pengajuan->nasabah)->nama }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            {{-- ================= INPUT PEMBIAYAAN ================= --}}
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            Data Pembiayaan
                                        </h3>
                                    </div>
                        
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label>Nomor Pembiayaan</label>
                                            <input type="text" class="form-control" value="{{ $nomorPembiayaan }}" readonly>
                                        </div>
                        
                                        <div class="mb-3">
                                            <label>Plafond</label>
                                            <input type="text" class="form-control" value="{{ number_format($pengajuan->plafond_disetujui,0,',','.') }}" readonly>
                                            <input type="hidden" id="plafond" name="plafond" value="{{ $pengajuan->plafond_disetujui }}">
                                        </div>
                        
                                        <div class="mb-3">
                                            <label>Tenor</label>
                                            <input type="text"class="form-control"value="{{ $pengajuan->tenor_disetujui }} Bulan"readonly>
                                            <input type="hidden" id="tenor" name="tenor" value="{{ $pengajuan->tenor_disetujui }}">
                                        </div>
                        
                                        <div class="mb-3">
                                            <label>Materai</label>
                                            <input type="number" class="form-control hitung"  id="materai" name="materai" value="10000">
                                        </div>
                        
                                        <div class="mb-3">
                                            <label>Biaya Survey</label>
                                            <input type="number" class="form-control hitung" id="biaya_survey" name="biaya_survey" value="0">
                                        </div>
                        
                                        <div class="mb-3">
                                            <label>Tanggal Jatuh Tempo Pertama</label>
                                            <input type="date" class="form-control" name="tanggal_jatuh_tempo_pertama" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            {{-- ================= HASIL ================= --}}
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            Hasil Perhitungan
                                        </h3>
                                    </div>
                        
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label>Jenis Tenor</label>
                                            <input id="jenis_tenor" class="form-control" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label>Persen Bunga</label>
                                            <input id="persen_bunga" class="form-control" readonly>
                                        </div>
                        
                                        <div class="mb-3">
                                            <label>Persen Administrasi</label>
                                            <input id="persen_administrasi" class="form-control" readonly>
                                        </div>
                        
                                        <div class="mb-3">
                                            <label>Biaya Administrasi</label>
                                            <input id="biaya_administrasi" class="form-control"readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label>Dana Diterima</label>
                                            <input id="dana_diterima" class="form-control" readonly>
                                        </div>

                                        <div id="panel-pendek" style="display:none;">
                                            <div class="alert alert-warning">
                                                <strong>Sistem Pembayaran Tenor Pendek</strong>
                                            </div>
                                            <div class="mb-3">
                                                <label>Bunga per Bulan</label>
                                                <input id="pendek_bunga" class="form-control" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label>Angsuran Bulan 1 s/d Sebelum Pelunasan</label>
                                                <input id="pendek_angsuran" class="form-control" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label>Pelunasan Bulan Terakhir</label>
                                                <input id="pendek_pelunasan" class="form-control"readonly>
                                            </div>
                                        </div>

                                        <div id="panel-panjang" style="display:none;">
                                            <div class="alert alert-info">
                                                <strong>Sistem Angsuran Tetap</strong>
                                            </div>
                                            <div class="mb-3">
                                                <label>Porsi Pokok / Bulan</label>
                                                <input id="panjang_pokok" class="form-control" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label>Porsi Bunga / Bulan</label>
                                                <input id="panjang_bunga" class="form-control" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label>Total Angsuran / Bulan</label>
                                                <input id="panjang_total" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button class="btn btn-primary">
                                Simpan Pembiayaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
$(function(){
    hitungPembiayaan();
    $('#materai,#biaya_survey').on('keyup change',function(){
        hitungPembiayaan();
    });
});

function hitungPembiayaan(){
    let plafond = parseFloat($('#plafond').val()) || 0;
    let tenor = parseInt($('#tenor').val()) || 0;
    let materai = parseFloat($('#materai').val()) || 0;
    let survey = parseFloat($('#biaya_survey').val()) || 0;
    let bunga = 0;
    let admin = plafond * 3 / 100;
    let diterima = plafond - admin - materai - survey;

    $('#persen_administrasi').val('3 %');
    $('#biaya_administrasi').val(formatRupiah(admin));
    $('#dana_diterima').val(formatRupiah(diterima));

    if(tenor <= 5){
        // TENOR PENDEK
        $('#panel-panjang').hide();
        $('#panel-pendek').show();
        $('#jenis_tenor').val('Pendek');
        bunga = plafond * 6 / 100;
        $('#persen_bunga').val('6 %');
        $('#pendek_bunga').val(formatRupiah(bunga));
        $('#pendek_angsuran').val(formatRupiah(bunga));
        $('#pendek_pelunasan').val(
            formatRupiah(plafond + bunga)
        );
    }else{
        // TENOR PANJANG
        $('#panel-pendek').hide();
        $('#panel-panjang').show();
        $('#jenis_tenor').val('Panjang');
        $('#persen_bunga').val('2.5 %');
        let pokok = plafond / tenor;
        let bunga = plafond * 2.5 / 100;
        let total = pokok + bunga;
        $('#panjang_pokok').val(formatRupiah(pokok));
        $('#panjang_bunga').val(formatRupiah(bunga));
        $('#panjang_total').val(formatRupiah(total));
    }
}

function formatRupiah(angka){
    return new Intl.NumberFormat('id-ID').format(angka);
}
</script>
@endsection