@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{route('hariliburs.create')}}" class="btn btn-info m-1"><i class="fas fa-plus-circle"></i> Tambah
                            </a>
                            <a href="{{route('hariliburs.monthly')}}" class="btn btn-info m-1"><i class="fas fa-plus-circle"></i> Tambah Perbulan
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive p-3">
                                <table id="mytable" class="table table-vcenter card-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nama Libur</th>
                                        <th>Tipe</th>
                                        <th>Berlaku Untuk</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($hariliburs as $libur)
                                    <tr>
                                        <td>
                                            {{ $libur->tanggal_mulai->format('d-m-Y') }}
                                            @if($libur->tanggal_selesai->ne($libur->tanggal_mulai))
                                                s/d {{ $libur->tanggal_selesai->format('d-m-Y') }}
                                            @endif
                                        </td>
                                        <td>{{ $libur->nama_libur }}</td>
                                        <td>{{ ucfirst($libur->tipe) }}</td>
                                        <td>{{ ucfirst($libur->berlaku_untuk) }}</td>
                                        <td>{{ $libur->keterangan ?? '-' }}</td>
                                        <td>
                                            <button class="btn btn-info btn-sm  btn-edit" data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="{{ $libur->id }}"
                                                data-tanggal_mulai="{{ $libur->tanggal_mulai }}"
                                                data-tanggal_selesai="{{ $libur->tanggal_selesai }}"
                                                data-nama_libur="{{ $libur->nama_libur }}"
                                                data-tipe="{{ $libur->tipe }}"
                                                data-berlaku_untuk="{{ $libur->berlaku_untuk }}"
                                                data-keterangan="{{ $libur->keterangan }}">Edit
                                            </button>
                                            <form action="{{ route('hariliburs.destroy', $libur->id) }}" method="POST"
                                                style="display:inline-block;"
                                                onsubmit="return confirm('Yakin hapus?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center">Belum ada data hari libur.</td></tr>
                                    @endforelse
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<div class="modal modal-blur fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Absensi Santri</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editLiburForm" method="POST" action="{{ route('hariliburs.update', ':id') }}">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label >Tanggal Mulai</label>
                    <input type="date" id="modal-tanggal-mulai" class="form-control" name="tanggal_mulai" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label >Tanggal Selesai</label>
                    <input type="date" id="modal-tanggal-selesai" class="form-control" name="tanggal_selesai" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label >Nama Libur</label>
                    <input type="text" id="modal-nama-libur" class="form-control" name="nama_libur" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Tipe</label>
                    <select id="modal-tipe" class="form-select" name="tipe" required style="width:100%">
                        <option value="nasional" {{ old('tipe') == 'nasional' ? 'selected' : '' }}>Nasional</option>
                        <option value="sekolah" {{ old('tipe') == 'sekolah' ? 'selected' : '' }}>sekolah</option>
                        <option value="mingguan" {{ old('tipe') == 'mingguan' ? 'selected' : '' }}>mingguan</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Berlaku Untuk</label>
                    <select id="modal-berlaku-untuk" class="form-select" name="berlaku_untuk" required style="width:100%">
                        <option value="semua" {{ old('berlaku_untuk') == 'semua' ? 'selected' : '' }}>semua</option>
                        <option value="siswa" {{ old('berlaku_untuk') == 'siswa' ? 'selected' : '' }}>siswa</option>
                        <option value="ustadz" {{ old('berlaku_untuk') == 'ustadz' ? 'selected' : '' }}>ustadz</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label >Keterangan</label>
                    <input type="text" id="modal-keterangan" name="keterangan" class="form-control">
                </div>
            </div>
            <!-- Submit Button -->
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Update Absensi</button>
                <button type="submit" class="btn me-auto" data-bs-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
        
      </div>
    </div>
  </div>

@endsection

@section('scripts')

<!-- SweetAlert2 Script -->
@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: "{{ session('success') }}",
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500
    });
</script>
@elseif (session('error'))
   <script>
       Swal.fire({
           icon: 'error',
           title: 'Oops...',
           text: "{{ session('error') }}",
           position: 'top-end',
           showConfirmButton: false,
           timer: 1500
       });
   </script>
@endif

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

$(document).ready(function () {
    // Ketika tombol edit di-klik
    $(document).on('click', '.btn-edit', function () {
        let liburId = $(this).data('id');  // Ambil ID dari data-id tombol edit

        // Ambil data absensi berdasarkan ID (via AJAX)
        $.ajax({
            url: `/hariliburs/${liburId}/edit`, // Pastikan URL edit benar
            type: 'GET',
            success: function (data) {
                // Cek data yang diterima (untuk debugging)
                console.log(data);  // Debugging data
                
                let tglMulai = new Date(data.tanggal_mulai);
                let tglSelesai = new Date(data.tanggal_selesai);

                // Isi modal dengan data yang diambil
                $('#modal-tanggal-mulai').val(tglMulai.toISOString().split('T')[0]); // Tanggal Mulai (format YYYY-MM-DD)
                $('#modal-tanggal-selesai').val(tglSelesai.toISOString().split('T')[0]); // Tanggal Selesai (format YYYY-MM-DD)
                $('#modal-nama-libur').val(data.nama_libur); // Keterangan
                $('#modal-tipe').val(data.tipe); // Keterangan
                $('#modal-berlaku-untuk').val(data.berlaku_untuk); // Keterangan
                $('#modal-keterangan').val(data.keterangan); // Keterangan

                // Update action URL form untuk update absensi
                $('#editLiburForm').attr('action', `/hariliburs/${data.id}`);
                
                // Tampilkan modal
                $('#editModal').modal('show');
            },
            error: function () {
                alert('Gagal mengambil data absensi');
            }
        });
    });
});

</script>
@endsection