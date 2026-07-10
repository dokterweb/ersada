@extends('layouts.app')

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
        <div class="row row-cards">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header bg-blue-lt">
                <h3 class="card-title">Data Pengajuan Pembiayaan</h3>
              </div>
              <div class="table-responsive p-3">
                <table id="mytable" class="table table-vcenter card-table">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>No Pengajuan</th>
                      <th>Tanggal</th>
                      <th>Nasabah</th>
                      <th>Marketing</th>
                      <th>Status</th>
                      <th class="w-1">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($pengajuans as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->nomor_pengajuan }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d-m-Y') }}</td>
                        <td>{{ $item->nasabah?->nama }}</td>
                        <td>{{ $item->marketing?->user?->name }}</td>
                        <td><span class="badge bg-primary">{{ str_replace('_',' ',$item->status) }}</span></td>
                        <td>
                          {{-- <a href="{{ route('pengajuan.show',$item->id) }}" class="btn btn-primary btn-sm">
                            Detail
                          </a> --}}
                          Act
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                Tidak ada data.
                            </td>
                        </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                {{ $pengajuans->links() }}
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