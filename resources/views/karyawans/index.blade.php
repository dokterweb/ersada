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
                <h3 class="card-title">Data Karyawan</h3>
              </div>
              <div class="table-responsive p-3">
                <table id="mytable" class="table table-vcenter card-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama Karyawan</th>
                      <th>NIK</th>
                      <th>No. HP</th>
                      <th>Cabang</th>
                      <th class="w-1">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($karyawans as $k)
                            <tr>
                              <td>{{$loop->iteration}}</td>
                              <td>{{ $k->user->name }}</td>
                              <td>{{ $k->nik }}</td>
                              <td>{{ $k->no_hp }}</td>
                              <td>{{ $k->cabang->nama_cabang}}</td>
                              <td>
                                  <div class="btn-list flex-nowrap">
                                    <a href="{{route('karyawans.edit',$k->id)}}" class="btn btn-sm btn-info"><i class="far fa-edit"></i></a>
                                    <a href="{{route('karyawans.show',$k->id)}}" class="btn btn-sm btn-success"><i class="fa-solid fa-eye"></i></a>
                                    <form method="POST" action="{{ route('karyawans.destroy', $k->id) }}" id="delete-form-{{ $k->id }}"> 
                                      @csrf
                                      @method('DELETE')
                                      <button type="button" class="btn btn-sm btn-danger" onclick="deleteConfirmation({{ $k->id }})">
                                          <i class="fas fa-trash-alt"></i>
                                      </button>
                                    </form> 
                                  </div>
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="6">No Data</td>
                        </tr>
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