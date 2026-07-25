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

<div class="modal fade" id="acceptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="acceptForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        Terima Tugas Survey
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tr>
                            <th>No Pengajuan</th>
                            <td id="modalPengajuan"></td>
                        </tr>
                        <tr>
                            <th>Nasabah</th>
                            <td id="modalNasabah"></td>
                        </tr>
                        <tr>
                            <th>Marketing</th>
                            <td id="modalMarketing"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-success">
                        Terima Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="startModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="startForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-play-circle"></i>
                        Mulai Survey
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        Setelah survey dimulai, status akan berubah menjadi
                        <strong>Sedang Survey</strong> dan Anda akan diarahkan
                        ke <strong>Pemeriksaan Berkas</strong>.
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th width="180">No Pengajuan</th>
                            <td id="startPengajuan"></td>
                        </tr>
                        <tr>
                            <th>Nasabah</th>
                            <td id="startNasabah"></td>
                        </tr>
                        <tr>
                            <th>Marketing</th>
                            <td id="startMarketing"></td>
                        </tr>
                        <tr>
                            <th>Cabang</th>
                            <td id="startCabang"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-primary"><i class="fa fa-play"></i>
                        Mulai Survey
                    </button>
                </div>
            </form>
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


$(function () {
  $('.btn-accept').click(function () {
      let id = $(this).data('id');
      $('#modalPengajuan').text($(this).data('pengajuan'));
      $('#modalNasabah').text($(this).data('nasabah'));
      $('#modalMarketing').text($(this).data('marketing'));
      $('#acceptForm').attr('action','/survey/' + id + '/accept');
      const modal = new bootstrap.Modal(
            document.getElementById('acceptModal')
        );
      $('#acceptModal').modal('show');
  });
});

$(function () {
    $('.btn-start').click(function () {
        let id = $(this).data('id');
        $('#startPengajuan').text($(this).data('pengajuan'));
        $('#startNasabah').text($(this).data('nasabah'));
        $('#startMarketing').text($(this).data('marketing'));
        $('#startCabang').text($(this).data('cabang'));
        $('#startForm').attr('action','/survey/' + id + '/start');
        const modal = new bootstrap.Modal(
            document.getElementById('startModal')
        );
        modal.show();
    });
});
</script>
@endsection