@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Wizard Survey
                    </h2>
                    <div class="text-secondary">
                        Survey Pengajuan Pembiayaan
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            @include('layouts.alert')
            <div class="row">
                {{-- Sidebar Wizard --}}
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-header">
                            <strong>Progress Survey</strong>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('survey.berkas',$survey) }}" class="list-group-item">
                                📁 Pemeriksaan Berkas
                            </a>
                         {{--  <a href="{{ route('survey.lapangan',$survey) }}" class="list-group-item">
                                🏠 Survey Lapangan
                            </a>
                            <a href="{{ route('survey.dokumentasi',$survey) }}" class="list-group-item">
                                📷 Dokumentasi
                            </a>
                            <a href="{{ route('survey.kesimpulan',$survey) }}" class="list-group-item">
                                📝 Kesimpulan
                            </a>
                            <a href="{{ route('survey.review',$survey) }}" class="list-group-item">
                                ✔ Review Survey
                            </a>  --}}
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="col-lg-9">
                    {{-- Ringkasan Pengajuan --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Ringkasan Pengajuan</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="170">No Pengajuan</th>
                                            <td>{{ $survey->pengajuan->nomor_pengajuan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Nasabah</th>
                                            <td>{{ optional($survey->pengajuan->nasabah)->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>Marketing</th>
                                            <td>{{ optional($survey->pengajuan->marketing)->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>Cabang</th>
                                            <td>{{ optional($survey->pengajuan->cabang)->nama }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="170">PIC Survey</th>
                                            <td>{{ optional($survey->assignedTo)->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status Survey</th>
                                            <td>
                                                @switch($survey->status)
                                                    @case('waiting')
                                                        <span class="badge bg-warning">Waiting</span>
                                                    @break
                                                    @case('accepted')
                                                        <span class="badge bg-info">Accepted</span>
                                                    @break
                                                    @case('progress')
                                                        <span class="badge bg-primary">Progress</span>
                                                    @break
                                                    @case('finished')
                                                        <span class="badge bg-success">Finished</span>
                                                    @break
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Mulai Survey</th>
                                            <td>{{ optional($survey->started_at)?->format('d-m-Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Isi Step --}}
                    <div class="card">
                        <div class="card-body">
                            @yield('survey-content')
                        </div>
                    </div>
                </div>
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