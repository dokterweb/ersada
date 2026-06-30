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
            <form action="{{ route('pengajuan.storeStep2',$pengajuan->id) }}" method="POST" class="card">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <ul class="steps steps-green steps-counter my-4">
                            <li class="step-item">
                                STEP 1
                            </li>
        
                            <li class="step-item active">
                                STEP 2
                            </li>
        
                            <li class="step-item">
                                STEP 3
                            </li>
        
                            <li class="step-item">
                                STEP 4
                            </li>
        
                            <li class="step-item">
                                REVIEW
                            </li>
        
                        </ul>
        
                    </div>
                <div class="card-header bg-success">
                    <h3 class="card-title">DATA NASABAH DAN PEKERJAAN</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nama Sesuai KTP</label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama',$pengajuan->nama) }}">
                            @error('nama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control" value="{{  old('nik',$pengajuan->nik) }}">
                            @error('nik')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir',$pengajuan->tempat_lahir) }}">
                            @error('tempat_lahir')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir',$pengajuan->tgl_lahir) }}">
                            @error('tgl_lahir')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="number" name="no_hp" class="form-control" value="{{ old('no_hp',$pengajuan->no_hp) }}">
                            @error('no_hp')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alamat</label>
                            <input type="text" name="alamat" class="form-control" value="{{ old('alamat',$pengajuan->alamat) }}">
                            @error('alamat')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status Perkawinan</label>
                            <select name="status_perkawinan" class="form-select">
                                <option value="menikah" {{ $pengajuan->status_perkawinan == 'menikah' ? 'selected' : '' }}>menikah</option>
                                <option value="belum_menikah" {{ $pengajuan->status_perkawinan == 'belum_menikah' ? 'selected' : '' }}>belum_menikah</option>
                            </select>
                            @error('status_perkawinan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jlh Tanggungan</label>
                            <input type="number" name="jumlah_tanggungan" class="form-control" value="{{ old('jumlah_tanggungan',$pengajuan->jumlah_tanggungan) }}">
                            @error('jumlah_tanggungan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status Rumah</label>
                            <select name="status_rumah" class="form-select">
                                <option value="milik_sendiri" {{ $pengajuan->status_rumah == 'milik_sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                                <option value="milik_keluarga" {{ $pengajuan->status_rumah == 'milik_keluarga' ? 'selected' : '' }}>Milik Keluarga</option>
                                <option value="dinas" {{ $pengajuan->status_rumah == 'dinas' ? 'selected' : '' }}>Dinas</option>
                                <option value="sewa" {{ $pengajuan->status_rumah == 'sewa' ? 'selected' : '' }}>sewa</option>
                                <option value="kost" {{ $pengajuan->status_rumah == 'kost' ? 'selected' : '' }}>Kost</option>
                            </select>
                            @error('status_rumah')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Lama Menetap (Tahun)</label>
                            <input type="number" name="lama_menetap_tahun" class="form-control" value="{{ old('lama_menetap_tahun',$pengajuan->lama_menetap_tahun) }}">
                            @error('lama_menetap_tahun')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Lama Menetap (bulan)</label>
                            <input type="number" name="lama_menetap_bulan" class="form-control" value="{{ old('lama_menetap_bulan',$pengajuan->lama_menetap_bulan) }}">
                            @error('lama_menetap_bulan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <hr>
                            <h1>Data Pekerjaan</h1>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jenis Pekerjaan</label>
                            <select name="jenis_pekerjaan" class="form-select">
                                <option value="wiraswasta" {{ $pengajuan->jenis_pekerjaan == 'wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                                <option value="karyawan" {{ $pengajuan->jenis_pekerjaan == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                            </select>
                            @error('jenis_pekerjaan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Penghasilan</label>
                            <input type="text" name="penghasilan" class="form-control" value="{{ old('penghasilan',$pengajuan->penghasilan) }}">
                            @error('penghasilan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nama Usaha</label>
                            <input type="text" name="nama_usaha" class="form-control" value="{{ old('nama_usaha',$pengajuan->nama_usaha) }}">
                            @error('nama_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jenis Usaha</label>
                            <input type="text" name="jenis_usaha" class="form-control" value="{{ old('jenis_usaha',$pengajuan->jenis_usaha) }}">
                            @error('jenis_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Lama Usaha</label>
                            <input type="text" name="lama_usaha" class="form-control" value="{{ old('lama_usaha',$pengajuan->lama_usaha) }}">
                            @error('lama_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jlh Pegawai</label>
                            <input type="text" name="jumlah_pegawai" class="form-control" value="{{ old('jumlah_pegawai',$pengajuan->jumlah_pegawai) }}">
                            @error('jumlah_pegawai')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Alamat Usaha</label>
                            <input type="text" name="alamat_usaha" class="form-control" value="{{ old('alamat_usaha',$pengajuan->alamat_usaha) }}">
                            @error('alamat_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Telephone Usaha</label>
                            <input type="text" name="telpon_usaha" class="form-control" value="{{ old('telpon_usaha',$pengajuan->telpon_usaha) }}">
                            @error('telpon_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Bangunan Usaha</label>
                            <select name="bangunan_usaha" class="form-select">
                                <option value="permanen" {{ $pengajuan->bangunan_usaha == 'permanen' ? 'selected' : '' }}>Permanen</option>
                                <option value="kpr" {{ $pengajuan->bangunan_usaha == 'kpr' ? 'selected' : '' }}>KPR</option>
                                <option value="kontrak" {{ $pengajuan->bangunan_usaha == 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                                <option value="biskon" {{ $pengajuan->bangunan_usaha == 'biskon' ? 'selected' : '' }}>Biskon</option>
                            </select>
                            @error('bangunan_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status Tempat Usaha</label>
                            <select name="status_tempat_usaha" class="form-select">
                                <option value="hak_milik" {{ $pengajuan->status_tempat_usaha == 'hak_milik' ? 'selected' : '' }}>Hak Milik</option>
                                <option value="semi_permanen" {{ $pengajuan->status_tempat_usaha == 'semi_permanen' ? 'selected' : '' }}>Semi Permanen</option>
                                <option value="tenda" {{ $pengajuan->status_tempat_usaha == 'tenda' ? 'selected' : '' }}>Tenda</option>
                                <option value="gerobak" {{ $pengajuan->status_tempat_usaha == 'gerobak' ? 'selected' : '' }}>Gerobak</option>
                                <option value="meja" {{ $pengajuan->status_tempat_usaha == 'meja' ? 'selected' : '' }}>Meja</option>
                            </select>
                            @error('status_tempat_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Aktivitas Usaha</label>
                            <select name="aktivitas_usaha" class="form-select">
                                <option value="ramai" {{ $pengajuan->aktivitas_usaha == 'ramai' ? 'selected' : '' }}>ramai</option>
                                <option value="sedang" {{ $pengajuan->aktivitas_usaha == 'sedang' ? 'selected' : '' }}>sedang</option>
                                <option value="sepi" {{ $pengajuan->aktivitas_usaha == 'sepi' ? 'selected' : '' }}>sepi</option>
                            </select>
                            @error('aktivitas_usaha')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('pengajuan.step1',$pengajuan->id) }}"class="btn btn-warning">Previous</a>
                    <button type="submit" class="btn btn-primary"> Simpan & Lanjut Step 3</button>
                </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection