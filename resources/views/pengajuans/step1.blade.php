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
            <form @if(isset($pengajuan)) action="{{ route('pengajuan.updateStep1',$pengajuan->id) }}"
                @else action="{{ route('pengajuan.store') }}"
                @endif method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <ul class="steps steps-green steps-counter my-4">
                            <li class="step-item active">STEP 1</li>
                            <li class="step-item">STEP 2</li>
                            <li class="step-item">STEP 3</li>
                            <li class="step-item">STEP 4</li>
                            <li class="step-item">REVIEW</li>
                        </ul>
        
                    </div>
                <div class="card-header bg-success">
                    <h3 class="card-title">DATA PENGAJUAN</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_pengajuan" class="form-control" value="{{ old('tanggal_pengajuan',$pengajuan->tanggal_pengajuan ?? '') }}">
                            @error('tanggal_pengajuan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nominal Pengajuan</label>
                            <input type="number" name="nominal_pengajuan" class="form-control" value="{{ old('nominal_pengajuan',$pengajuan->nominal_pengajuan ?? '') }}">
                            @error('nominal_pengajuan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tenor</label>
                            <input type="number" name="tenor" class="form-control" value="{{ old('tenor',$pengajuan->tenor ?? '') }}">
                            @error('tenor')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Kategori Nasabah</label>
                            <select name="kategori_nasabah" class="form-select">
                                <option value="payroll" @if(old('kategori_nasabah',$pengajuan->kategori_nasabah ?? '') == 'payroll') selected @endif>Dinas / Perkebunan (Potong ATM)</option>
                                <option value="non_payroll" @if(old('kategori_nasabah',$pengajuan->kategori_nasabah ?? '') == 'non_payroll') selected @endif>Umum (Tanpa ATM)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tujuan Pinjaman</label>
                            <input type="text" name="tujuan_pinjaman" class="form-control" value="{{ old('tujuan_pinjaman',$pengajuan->tujuan_pinjaman ?? '') }}">
                            @error('tujuan_pinjaman')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Catatan</label>
                            <input type="text" name="catatan" class="form-control" value="{{ old('catatan',$pengajuan->catatan ?? '') }}">
                            @error('catatan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary"> Simpan & Lanjut Step 2</button>
                </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection