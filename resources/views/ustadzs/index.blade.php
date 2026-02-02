@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            <form action="{{ route('ustadzs.store') }}" method="POST" enctype="multipart/form-data" class="card">
              @csrf
              <div class="card-header bg-red-lt">
                <h3 class="card-title">Input Data</h3>
              </div>
              <div class="card-body">
                  <div class="row">
                      <div class="col-md-4 mb-3">
                          <div class="form-group">
                              <label>Nama Ustadz / Ustadzah</label>
                              <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                              @error('name')
                                  <span class="text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>
                      <div class="col-md-4 mb-3">
                          <div class="form-group">
                              <label>Kelompok</label>
                              <select class="form-select" name="kelompok_id">
                                  <option value="">Pilih kelompok</option>
                                  @foreach ($kelompoks as $p)
                                  <option value="{{ $p->id }}" {{ old('kelompok_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_kelompok }}</option>
                                  @endforeach
                              </select>
                              @error('kelompok_id')
                                  <span class="text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>
                      <div class="col-md-4 mb-3">
                          <div class="form-group">
                              <label>Kelamin</label>
                              <select name="kelamin" class="form-select">
                                  <option value="laki-laki" {{ old('kelamin') == 'laki-laki' ? 'selected' : '' }}>laki-laki</option>
                                  <option value="perempuan" {{ old('kelamin') == 'perempuan' ? 'selected' : '' }}>perempuan</option>
                              </select>
                              @error('kelamin')
                                  <span class="text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>
                      <div class="col-md-4 mb-3">
                          <div class="form-group">
                              <label >Email</label>
                              <input type="text" name="email" class="form-control" value="{{ old('email') }}">
                              @error('email')
                                  <span class="text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>
                      <div class="col-md-4 mb-3">
                          <div class="form-group">
                              <label >Password</label>
                              <input type="password" name="password" class="form-control">
                              @error('password')
                                  <span class="text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>
                      <div class="col-md-4 mb-3">
                        <div class="form-group">
                          <label for="avatar">Avatar</label>
                          <input type="file" class="form-control" name="avatar" accept="image/*">
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
                <h3 class="card-title">Data Ustadz</h3>
              </div>
              <div class="table-responsive p-3">
                <table id="mytable" class="table table-vcenter card-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama Ustadz</th>
                      <th>Kelompok</th>
                      <th>Kelamin</th>
                      <th>Email</th>
                      <th class="w-1">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($ustadzs as $p)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$p->user->name}} </td>
                            <td>{{$p->kelompok->nama_kelompok}} </td>
                            <td>{{$p->kelamin}} </td>
                            <td>{{$p->user->email}} </td>
                            <td class="d-flex align-items-center" style="gap: 5px;">
                                <button type="button" class="btn btn-sm btn-primary" 
                                        data-bs-toggle="modal" data-bs-target="#modalnya" 
                                        onclick="showUstadzDetails({{ $p->id }})">
                                    Show
                                </button>
                                <a href="{{route('ustadzs.edit',$p->id)}}" class="btn btn-sm btn-info">Edit</a>
                                <form method="POST" action="{{ route('ustadzs.destroy', $p->id) }}" style="display: inline;" id="delete-form-{{ $p->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteConfirmation({{ $p->id }})">
                                        Del
                                    </button>
                                </form>
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

<div class="modal modal-blur fade" id="modalnya" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="ustadz-details">
            
            <!-- Konten Ustadz akan dimuat melalui JavaScript -->
            <div class="row">
                <div class="col-md-4">
                    <img id="ustadz-avatar" src="" alt="Avatar" class="img-fluid rounded-circle">
                </div>
                <div class="col-md-8">
                    <table class="table table-vcenter card-table">
                        <tr>
                            <td>Nama Ustadz</td>
                            <td>:</td>
                            <td><span id="ustadz-name"></span></td>
                        </tr>
                        <tr>
                            <td>Kelompok</td>
                            <td>:</td>
                            <td><span id="ustadz-kelompok"></span></td>
                        </tr>
                        <tr>
                            <td>Kelamin</td>
                            <td>:</td>
                            <td><span id="ustadz-kelamin"></span></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>:</td>
                            <td><span id="ustadz-email"></span></td>
                        </tr>
                    </table>
                </div>
            </div>
            
        </div>
        <div class="modal-footer">
          <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
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

    function showUstadzDetails(id) {
        // Melakukan request AJAX untuk mendapatkan detail Ustadz berdasarkan ID
        $.ajax({
            url: '/ustadzs/' + id, // Rute untuk mendapatkan detail ustadz berdasarkan ID
            type: 'GET',
            success: function(response) {
                // Mengisi modal dengan data Ustadz
                $('#ustadz-name').text(response.name);
                $('#ustadz-kelompok').text(response.kelompok);
                $('#ustadz-kelamin').text(response.kelamin);
                $('#ustadz-email').text(response.email);
                
                // Menampilkan avatar jika ada
                 if (response.avatar) {
                    $('#ustadz-avatar').attr('src', response.avatar); // Mengatur URL avatar
                } else {
                    $('#ustadz-avatar').attr('src', '/storage/avatar/default-avatar.png'); // Default avatar jika tidak ada
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat memuat data');
            }
        });
    }
    
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