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
            <form action="{{ route('pengajuans.store') }}" method="POST" enctype="multipart/form-data"  class="card">
                @csrf
                <div class="card-header bg-cyan">
                    <h3 class="card-title">DATA PENGAJUAN</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_pengajuan" class="form-control" value="{{ old('tanggal_pengajuan') }}">
                            @error('tanggal_pengajuan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nominal Pengajuan</label>
                            <input type="number" name="nominal_pengajuan" class="form-control" value="{{ old('nominal_pengajuan') }}">
                            @error('nominal_pengajuan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tenor</label>
                            <input type="number" name="tenor" class="form-control" value="{{ old('tenor') }}">
                            @error('tenor')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tujuan Pinjaman</label>
                            <input type="text" name="tujuan_pinjaman" class="form-control" value="{{ old('tujuan_pinjaman') }}">
                            @error('tujuan_pinjaman')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Catatan</label>
                            <input type="text" name="catatan" class="form-control" value="{{ old('catatan') }}">
                            @error('catatan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <hr>
                            <h1>Data Nasabah</h1>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nama Sesuai KTP</label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}">
                            @error('nama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control" value="{{ old('nik') }}">
                            @error('nik')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}">
                            @error('tempat_lahir')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir') }}">
                            @error('tgl_lahir')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="number" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
                            @error('no_hp')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alamat</label>
                            <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}">
                            @error('alamat')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status Perkawinan</label>
                            <select name="status_perkawinan" class="form-select">
                                <option value="menikah" {{ old('status_perkawinan') == 'menikah' ? 'selected' : '' }}>Menikah</option>
                                <option value="belum_menikah" {{ old('status_perkawinan') == 'belum_menikah' ? 'selected' : '' }}>Belum Menikah</option>
                            </select>
                            @error('status_perkawinan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jlh Tanggungan</label>
                            <input type="number" name="jumlah_tanggungan" class="form-control" value="{{ old('jumlah_tanggungan') }}">
                            @error('jumlah_tanggungan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status Rumah</label>
                            <select name="status_rumah" class="form-select">
                                <option value="milik_sendiri" {{ old('status_rumah') == 'milik_sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                                <option value="milik_keluarga" {{ old('status_rumah') == 'milik_keluarga' ? 'selected' : '' }}>Milik Keluarga</option>
                                <option value="dinas" {{ old('status_rumah') == 'dinas' ? 'selected' : '' }}>Dinas</option>
                                <option value="sewa" {{ old('status_rumah') == 'sewa' ? 'selected' : '' }}>sewa</option>
                                <option value="kost" {{ old('status_rumah') == 'kost' ? 'selected' : '' }}>Kost</option>
                            </select>
                            @error('status_rumah')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Lama Menetap (Tahun)</label>
                            <input type="number" name="lama_menetap_tahun" class="form-control" value="{{ old('lama_menetap_tahun') }}">
                            @error('lama_menetap_tahun')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Lama Menetap (bulan)</label>
                            <input type="number" name="lama_menetap_bulan" class="form-control" value="{{ old('lama_menetap_bulan') }}">
                            @error('lama_menetap_bulan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <hr>
                            <h1>Upload Dokumen</h1>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">KTP</label>
                            <input type="file" name="dokumen[ktp]" class="form-control">
                            @error('dokumen.ktp')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kartu Keluarga</label>
                            <input type="file" name="dokumen[kk]" class="form-control">
                            @error('dokumen.kk')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Slip Gaji</label>
                            <input type="file" name="dokumen[slip_gaji]" class="form-control">
                            @error('dokumen.slip_gaji')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>                       
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection

@section('scripts')
<script>

    $(document).ready(function() {
        // Inisialisasi DataTables
        $('#mytable').DataTable({
            "processing": true,   // Menampilkan loading saat memproses data
            "serverSide": false,  // Tentukan apakah menggunakan server-side processing
            "paging": true,       // Menampilkan pagination
            "lengthChange": false // Menonaktifkan pengaturan jumlah baris per halaman
        });
    });

    function deleteConfirmation(id) {
        // SweetAlert2 konfirmasi
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirimkan form untuk menghapus data jika dikonfirmasi
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection