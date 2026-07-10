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
              <div class="card-body">

                <form method="GET">
    
                    <div class="row">
    
                        <div class="col-md-5">
    
                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Nomor Pengajuan / Nama Nasabah"
                                value="{{ request('search') }}">
    
                        </div>
    
                        <div class="col-md-3">
    
                            <select
                                name="status"
                                class="form-select">
    
                                <option value="">Menunggu Review</option>
    
                                <option value="menunggu_pimpinan"
                                    @selected(request('status')=='menunggu_pimpinan')>
    
                                    Menunggu Pimpinan
    
                                </option>
    
                                <option value="ditolak_pimpinan"
                                    @selected(request('status')=='ditolak_pimpinan')>
    
                                    Ditolak
    
                                </option>
    
                                <option value="didisposisi_spvmarketing"
                                    @selected(request('status')=='didisposisi_spvmarketing')>
    
                                    Disposisi SPV Marketing
    
                                </option>
    
                                <option value="didisposisi_surveyor"
                                    @selected(request('status')=='didisposisi_surveyor')>
    
                                    Disposisi Surveyor
    
                                </option>
    
                            </select>
    
                        </div>
    
                        <div class="col-md-2">
    
                            <button class="btn btn-primary w-100">
    
                                Cari
    
                            </button>
    
                        </div>
    
                    </div>
    
                </form>
    
            </div>
    
            <div class="table-responsive">
    
                <table class="table table-hover table-vcenter">
    
                    <thead>
    
                        <tr>
    
                            <th>No</th>
    
                            <th>No Pengajuan</th>
    
                            <th>Nasabah</th>
    
                            <th>Marketing</th>
    
                            <th>Cabang</th>
    
                            <th>Plafond</th>
    
                            <th>Status</th>
    
                            <th>Aksi</th>
    
                        </tr>
    
                    </thead>
    
                    <tbody>
    
                    @forelse($pengajuans as $item)
    
                        <tr>
    
                            <td>
    
                                {{ $pengajuans->firstItem()+$loop->index }}
    
                            </td>
    
                            <td>
    
                                {{ $item->nomor_pengajuan }}
    
                            </td>
    
                            <td>
    
                                {{ $item->nasabah?->nama }}
    
                            </td>
    
                            <td>
    
                                {{ $item->marketing?->user?->name }}
    
                            </td>
    
                            <td>
    
                                {{ $item->cabang?->nama }}
    
                            </td>
    
                            <td>
    
                                {{ number_format($item->plafond) }}
    
                            </td>
    
                            <td>
    
                                <span class="badge {{ $item->status_badge['class'] }}">
    
                                    {{ $item->status_badge['label'] }}
    
                                </span>
    
                            </td>
    
                            <td>
    
                                <a
                                    href="{{ route('pimpinan.show',$item) }}"
                                    class="btn btn-primary btn-sm">
    
                                    Review
    
                                </a>
    
                            </td>
    
                        </tr>
    
                    @empty
    
                        <tr>
    
                            <td colspan="8" class="text-center">
    
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