@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Kelas
            </h2>
          </div>
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-4">
            <form class="card" method="POST" action="{{route('cabangs.update',$cabang->id)}}">
              @csrf
              @method('PUT')
              <div class="card-header">
                <h3 class="card-title">Edit Data</h3>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label">Kode</label>
                  <input type="text" name="kode" class="form-control" value="{{$cabang->kode}}">
                </div>
                <div class="mb-3">
                  <label class="form-label">Nama Cabang</label>
                  <input type="text" name="nama_cabang" class="form-control" value="{{$cabang->nama_cabang}}">
                </div>
                <div class="mb-3">
                  <label class="form-label">Alamat</label>
                  <input type="text" name="alamat" class="form-control" value="{{$cabang->alamat}}">
                </div>
              </div>
              <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Update</button>
              </div>
            </form>
          </div>

          <div class="col-lg-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Data Kelas</h3>
              </div>
              <div class="table-responsive">
                <table id="mytable" class="table table-vcenter card-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Kode</th>
                      <th>Nama Cabang</th>
                      <th>Alamat</th>
                      <th class="w-1">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($cabangview as $m)
                      <tr>
                          <td>{{$loop->iteration}}</td>
                          <td>{{$m->kode}} </td>
                          <td>{{$m->nama_cabang}} </td>
                          <td>{{$m->alamat}} </td>
                          <td>
                            <div class="btn-list flex-nowrap">
                              <a href="{{route('cabangs.edit',$m->id)}}" class="btn btn-sm btn-info"><i class="far fa-edit"></i>Edit</a>
                              {{-- <form method="POST" action="{{ route('kelasnyas.destroy', $m->id) }}" style="display: inline;" id="delete-form-{{ $m->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteConfirmation({{ $m->id }})">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                              </form> --}}
                            </div>
                          </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="3">No Data</td>
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