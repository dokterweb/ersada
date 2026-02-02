@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            <form action="{{ route('absensisantris.index') }}" method="GET" class="card">
              @csrf
              <div class="card-header bg-red-lt">
                <h3 class="card-title">Filter Tanggal</h3>
              </div>
              <div class="card-body">
                  <div class="row">
                      <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <label for="tanggal_mulai">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', $tanggalMulai->toDateString()) }}">
                          </div>
                      </div>
                      <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <label for="tanggal_selesai">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', $tanggalSelesai->toDateString()) }}">
                          </div>
                      </div>
                     
                  </div>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </form>
          </div>

          <div class="col-lg-12">
            <div class="card">
              <div class="card-header bg-cyan-lt">
                <h3 class="card-title">Data Absen</h3>
              </div>
              <div class="table-responsive p-3">
                <table id="mytable" class="table table-vcenter card-table">
                  <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal Absensi</th>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($absensiSantris as $absensi)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($absensi->tgl_absen)->format('d-m-Y') }}</td>
                            <td>{{ $absensi->santri->user->name }}</td>
                            <td>{{ ucfirst($absensi->status) }}</td>
                            <td>{{ $absensi->keterangan ?? '-' }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm  btn-edit" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="{{ $absensi->id }}"
                                    data-status="{{ $absensi->status }}"
                                    data-keterangan="{{ $absensi->keterangan }}"
                                    data-tgl_absen="{{ $absensi->tgl_absen }}"
                                    data-siswa-nama="{{ $absensi->santri->user->name }}">Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="modal modal-blur fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Absensi Santri</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editAbsensiForm" method="POST" action="{{ route('absensisantris.update', ':id') }}">
          @csrf
          @method('PUT')
          
          <!-- Nama Santri (readonly) -->
          <div class="mb-3">
              <label class="form-label">Nama Santri</label>
              <input type="text" class="form-control" id="modal-nama-santri" readonly>
          </div>

          <!-- Tanggal Absen (input) -->
          <div class="mb-3">
              <label class="form-label">Tanggal Absen</label>
              <input type="date" class="form-control" id="modal-tanggal-absen" name="tgl_absen" required>
          </div>

          <!-- Status Kehadiran (radio button) -->
          <div class="mb-3">
              <label class="form-label">Status Kehadiran</label><br>
              <label class="me-3">
                  <input type="radio" name="status" value="hadir" id="status-hadir" required> Hadir
              </label>
              <label class="me-3">
                  <input type="radio" name="status" value="ghoib" id="status-ghoib" required> Ghoib
              </label>
              <label class="me-3">
                  <input type="radio" name="status" value="sakit" id="status-sakit" required> Sakit
              </label>
              <label class="me-3">
                  <input type="radio" name="status" value="izin" id="status-izin" required> Izin
              </label>
          </div>

          <!-- Keterangan (input) -->
          <div class="mb-3">
              <label class="form-label">Keterangan</label>
              <input type="text" class="form-control" id="modal-keterangan" name="keterangan">
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
      let absensiId = $(this).data('id');  // Ambil ID dari data-id tombol edit

      // Ambil data absensi berdasarkan ID (via AJAX)
      $.ajax({
          url: `/absensi-santri/${absensiId}/edit`, // Pastikan URL edit benar
          type: 'GET',
          success: function (data) {
              // Cek data yang diterima (untuk debugging)
              console.log(data);  // Debugging data

              // Isi modal dengan data yang diambil
              $('#modal-nama-santri').val(data.santri.user.name); // Nama Santri
              $('#modal-tanggal-absen').val(data.tgl_absen); // Tanggal Absen
              $('#modal-keterangan').val(data.keterangan); // Keterangan

              // Set radio button status sesuai dengan data yang ada
              $('input[name="status"][value="'+data.status+'"]').prop('checked', true);

              // Update action URL form untuk update absensi
              $('#editAbsensiForm').attr('action', `/absensi-santri/${data.id}`);
              
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