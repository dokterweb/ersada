@extends('layouts.app')
@section('title','Modul Survey')
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
          <!-- Page title actions -->
          <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
              <a href="{{route('karyawans.create')}}" class="btn btn-primary">
                Tambah Karyawan
            </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">
                    <i class="fa fa-map-marker-alt"></i>
                    Modul Survey
                </h4>
                <small class="text-muted">
                    @role('spvsurveyor')
                        Daftar pengajuan yang menunggu proses survey
                    @elserole('surveyor')
                        Daftar tugas survey Anda
                    @endrole
                </small>
            </div>
        </div>
    
        @include('layouts.alert')
        @role('spvsurveyor')
            @include('survey.partials.spv-table')
        @elserole('surveyor')
            @include('survey.partials.surveyor-table')
        @endrole
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