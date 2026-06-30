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
            <form action="{{ route('pengajuan.storeKapital',$pengajuan->id) }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <ul class="steps steps-green steps-counter my-4">
                            <li class="step-item">STEP 2</li>
                            <li class="step-item">STEP 3</li>
                            <li class="step-item">STEP 4</li>
                            <li class="step-item">REVIEW</li>
                            <li class="step-item">REVIEW</li>
                            <li class="step-item">ANALISA</li>
                            <li class="step-item">JAMINAN</li>
                            <li class="step-item active">ANALISA KAPITAL</li>
                        </ul>
        
                    </div>
                <div class="card-header bg-success">
                    <h3 class="card-title">DATA PENGAJUAN</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Omzet Harian</label>
                            <input type="number" name="omzet_harian" class="form-control" value="{{ old('omzet_harian', $kapital?->omzet_harian) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Laba Harian</label>
                            <input type="number" name="laba_harian" class="form-control" value="{{ old('laba_harian', $kapital?->laba_harian) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Pendapatan Lain</label>
                            <input type="number" name="pendapatan_lain" class="form-control" value="{{ old('pendapatan_lain', $kapital?->pendapatan_lain) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Pendapatan Pasangan</label>
                            <input type="number" name="pendapatan_pasangan" class="form-control" value="{{ old('pendapatan_pasangan', $kapital?->pendapatan_pasangan) }}">
                        </div>
                    </div>
                    <hr>
                    <h4>Pengeluaran</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Biaya Rumah Tangga</label>
                            <input type="number" name="biaya_rumah_tangga" class="form-control" value="{{ old('biaya_rumah_tangga', $kapital?->biaya_rumah_tangga) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Biaya Motor</label>
                            <input type="number" name="biaya_motor" class="form-control" value="{{ old('biaya_motor', $kapital?->biaya_motor) }}">
                        </div>
        
                        <div class="col-md-4 mb-3">
                            <label>Biaya Koperasi</label>
                            <input type="number" name="biaya_koperasi" class="form-control" value="{{ old('biaya_koperasi', $kapital?->biaya_koperasi) }}">
                        </div>
        
                        <div class="col-md-4 mb-3">
                            <label>Angsuran Lain</label>
                            <input type="number" name="angsuran_lain" class="form-control" value="{{ old('angsuran_lain', $kapital?->angsuran_lain) }}">
                        </div>
        
                        <div class="col-md-4 mb-3">
                            <label>Biaya Kontrak Rumah</label>
                            <input type="number" name="biaya_kontrak_rumah" class="form-control" value="{{ old('biaya_kontrak_rumah',$kapital?->biaya_kontrak_rumah) }}">
                        </div>
        
                        <div class="col-md-4 mb-3">
                            <label>Biaya Tempat Usaha</label>
                            <input type="number" name="biaya_tempat_usaha" class="form-control" value="{{ old('biaya_tempat_usaha',$kapital?->biaya_tempat_usaha) }}">
                        </div>
        
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('pengajuan.jaminan',$pengajuan->id) }}" class="btn btn-warning">
                         Previous
                     </a>
                     <button class="btn btn-primary">
                         Simpan & Lanjut Survey
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