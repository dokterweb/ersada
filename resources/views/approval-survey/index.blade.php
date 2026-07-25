@extends('layouts.app')
@section('title','Approval Survey')
@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Approval HasilSurvey
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
        <div class="row row-cards">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-blue-lt">
                        <h3 class="card-title">Data Pengajuan Pembiayaan</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter table-striped">
                                <thead>
                                    <tr>
                                        <th width="50">No</th>
                                        <th>No Pengajuan</th>
                                        <th>Nasabah</th>
                                        <th>Marketing</th>
                                        <th>Surveyor</th>
                                        <th>Tanggal Submit</th>
                                        <th>Status Survey</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($pengajuans as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nomor_pengajuan }}</td>
                                        <td>{{ optional($item->nasabah)->nama }}</td>
                                        <td>{{ optional($item->marketing)->user->name }}</td>
                                        <td>{{ optional($item->survey->assignedTo)->name }}</td>
                                        <td>{{ optional($item->survey->submitted_at)?->format('d-m-Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-warning">
                                                {{ strtoupper($item->survey->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('approvalSurvey.show',$item) }}" class="btn btn-primary btn-sm">
                                                Review
                                            </a> 
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            Tidak ada survey yang menunggu approval.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($pengajuans->hasPages())
                        <div class="card-footer">
                            {{ $pengajuans->links() }}
                        </div>
                        @endif
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