@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            <div class="card">
            <form action="{{ route('karyawans.store') }}" method="POST" enctype="multipart/form-data"  class="card">
                @csrf
                <div class="card-header bg-cyan">
                    <h3 class="card-title">Data Siswa</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nama Karyawan</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                            @error('name')
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
                            <label class="form-label">Cabang</label>
                            <select class="form-select" name="cabang_id">
                              <option value="">Pilih Cabang</option>
                              @foreach ($cabangs as $p)
                              <option value="{{ $p->id }}" {{ old('cabang_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_cabang }}</option>
                              @endforeach
                            </select>
                            @error('cabang_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tgl_masuk" class="form-control" value="{{ old('tgl_masuk') }}">
                            @error('tgl_masuk')
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
                            <label class="form-label">Kelamin</label>
                            <select name="kelamin" class="form-select">
                                <option value="laki-laki" {{ old('kelamin') == 'laki-laki' ? 'selected' : '' }}>laki-laki</option>
                                <option value="perempuan" {{ old('kelamin') == 'perempuan' ? 'selected' : '' }}>perempuan</option>
                            </select>
                            @error('kelamin')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ old('status') == 'active' ? 'selected' : '' }}>active</option>
                                <option value="0" {{ old('status') == 'non_active' ? 'selected' : '' }}>non_active</option>
                            </select>
                            @error('status')
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
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role">
                           <option value="">Pilih Role</option>
                            @foreach($roles as $role)
                                @php
                                $label = match($role->name){
                                    'kacab' => 'Kepala Cabang',
                                    'spvmarketing' => 'SPV Marketing',
                                    'spvsurveyor' => 'SPV Surveyor',
                                    default => ucwords($role->name)
                                };
                                @endphp
                                <option value="{{ $role->name }}"
                                    {{ old('role') == $role->name ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                            </select>
                            @error('role')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                       <div class="col-md-6 mb-3">
                            <label class="form-label">alamat</label>
                            <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}">
                            @error('alamat')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-header bg-lime">
                    <h3 class="card-title">Data Login</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" name="email" class="form-control" value="{{ old('email') }}">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" value="{{ old('password') }}">
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Avatar</label>
                            <input type="file" name="avatar" class="form-control">
                            @error('avatar')
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