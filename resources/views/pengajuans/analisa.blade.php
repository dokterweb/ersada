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
            <form action="{{ route('pengajuan.storeAnalisa',$pengajuan->id) }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <ul class="steps steps-green steps-counter my-4">
                            <li class="step-item">STEP 1</li>
                            <li class="step-item">STEP 2</li>
                            <li class="step-item">STEP 3</li>
                            <li class="step-item">STEP 4</li>
                            <li class="step-item">REVIEW</li>
                            <li class="step-item active">ANALISA</li>
                        </ul>
        
                    </div>
                    <div class="card-header bg-success">
                        <h3 class="card-title">ANALISA PENGAJUAN</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Harga Kredit</label>
                                <select name="harga_kredit" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="1" {{ old('harga_kredit',$analisa?->harga_kredit) == 1 ? 'selected' : '' }}>
                                        Setuju
                                    </option>
                                    <option value="0" {{ old('harga_kredit', $analisa?->harga_kredit) == 0 ? 'selected' : '' }}>
                                        Tidak Setuju
                                    </option>
                                </select>
                                @error('harga_kredit')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kewajiban Angsuran</label>
                                <select name="kewajiban_angsuran" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="1" {{ old('kewajiban_angsuran', $analisa?->kewajiban_angsuran) == 1? 'selected' : '' }}>
                                        Sudah Dijelaskan
                                    </option>
                                    <option value="0" {{ old('kewajiban_angsuran', $analisa?->kewajiban_angsuran) == 0? 'selected' : '' }}>
                                        Belum
                                    </option>
                                </select>
                                @error('kewajiban_angsuran')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status Pemohon</label>
                                <select name="status_pemohon" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="pemilik" {{ old('status_pemohon', $analisa?->status_pemohon) == 'pemilik'? 'selected' : '' }}>
                                        Pemilik
                                    </option>
                                    <option value="karyawan" {{ old('status_pemohon', $analisa?->status_pemohon) == 'karyawan'? 'selected' : '' }}>
                                        Karyawan
                                    </option>
                                    <option value="pengelola" {{ old('status_pemohon', $analisa?->status_pemohon) == 'pengelola' ? 'selected' : '' }}>
                                        Pengelola
                                    </option>
                                </select>
                                @error('status_pemohon')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status TU / RM</label>
                                <select name="status_tempat_tinggal" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="milik" {{ old('status_tempat_tinggal', $analisa?->status_tempat_tinggal) == 'milik'? 'selected' : '' }}>
                                        Milik
                                    </option>
                                    <option value="kontrak" {{ old('status_tempat_tinggal', $analisa?->status_tempat_tinggal) == 'kontrak' ? 'selected' : '' }}>
                                        Kontrak
                                    </option>
                                </select>
                                @error('status_tempat_tinggal')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Data Pemohon</label>
                                <select name="status_tempat_tinggal" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="1" {{ old('data_pemohon_lengkap', $analisa?->data_pemohon_lengkap) == 1 ? 'selected' : '' }}>
                                        Lengkap
                                    </option>
                                    <option value="0" {{ old('data_pemohon_lengkap', $analisa?->data_pemohon_lengkap) == 0 ? 'selected' : '' }}>
                                        Tidak Lengkap
                                    </option>
                                </select>
                                @error('status_tempat_tinggal')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        
                        </div>
                        <div class="hr-text hr-text-center hr-text-spaceless p-6"><h4>VALIDASI DOKUMEN</h4></div>
                        <div class="row">
                            {{-- KTP PEMOHON --}}
                            <div class="col-md-3 mb-3">
                                <label>KTP Pemohon</label>
                                <select name="ktp_pemohon_valid" class="form-select">
                                    <option value="1" {{ old('ktp_pemohon_valid', $analisa?->ktp_pemohon_valid) == 1 ? 'selected':'' }}>
                                        Valid
                                    </option>
                                    <option value="0" {{ old('ktp_pemohon_valid', $analisa?->ktp_pemohon_valid) == 0 ? 'selected':'' }}>
                                        Tidak Valid
                                    </option>
                                </select>
                            </div>
                            {{-- KTP PASANGAN --}}
                            <div class="col-md-3 mb-3">
                                <label>KTP Pasangan</label>
                                <select name="ktp_pasangan_valid" class="form-select">
                                    <option value="1" {{ old('ktp_pasangan_valid', $analisa?->ktp_pasangan_valid) == 1? 'selected':'' }}>
                                        Valid
                                    </option>
                                    <option value="0" {{ old('ktp_pasangan_valid', $analisa?->ktp_pasangan_valid) == 0 ? 'selected':'' }}>
                                        Tidak Valid
                                    </option>
                                </select>
                            </div>
                            {{-- KK --}}
                            <div class="col-md-3 mb-3">
                                <label>Kartu Keluarga</label>
                                <select name="kk_valid" class="form-select">
                                    <option value="1" {{ old('kk_valid', $analisa?->kk_valid) == 1 ? 'selected':'' }}>
                                        Valid
                                    </option>
                                    <option value="0" {{ old('kk_valid', $analisa?->kk_valid) == 0 ? 'selected':'' }}>
                                        Tidak Valid
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Kartu Keluarga</label>
                                <select name="perbaikan_plafon" class="form-control">
                                    <option value="0"{{ old('perbaikan_plafon',$analisa?->perbaikan_plafon) == 0? 'selected' : '' }}>
                                        Belum
                                    </option>
                                    <option value="1" {{ old('perbaikan_plafon', $analisa?->perbaikan_plafon) == 1 ? 'selected' : '' }}>
                                        Sudah
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('pengajuan.reviewData',$pengajuan->id) }}" class="btn btn-warning">
                             Previous
                         </a>
                         <button type="submit" class="btn btn-primary">
                             Simpan & Lanjut Jaminan
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