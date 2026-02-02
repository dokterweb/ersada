@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-cyan-lt">
                          <h3 class="card-title">Detail Santri - {{ $santri->user->name }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Nama santri</label>
                                        <input type="text" class="form-control" value="{{ $santri->user->name }}" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label>Ustadz</label>
                                        <input type="text" class="form-control" value="{{ optional(optional($santri->ustadz)->user)->name ?? '-' }}" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label>Kelompok</label>
                                        <input type="text" class="form-control" value="{{ $santri->kelompok->nama_kelompok }}" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label>Kelas</label>
                                        <input type="text" class="form-control" value="{{ $santri->kelasnya->nama_kelas }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex align-items-center justify-content-center">
                                    <div class="text-center">
                                        @if(!empty($santri->user->avatar))
                                            <img src="{{ asset('storage/' . $santri->user->avatar) }}"
                                                 alt="Avatar Santri"
                                                 class="img-thumbnail rounded-circle"
                                                 style="width: 250px; height: 250px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('images/default-avatar.png') }}"
                                                 alt="Default Avatar"
                                                 class="img-thumbnail rounded-circle"
                                                 style="width: 250px; height: 250px; object-fit: cover;">
                                        @endif
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mt-4">
                    <div class="card">
                        <div class="card-header bg-cyan-lt">
                            <h3 class="card-title">Data Murojaah</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive p-3">
                                <table id="mytable" class="table table-vcenter card-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Nama Surat</th>
                                        <th>Dari Ayat</th>
                                        <th>Sampai Ayat</th>
                                        <th>Keterangan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($murojaahHistories as $history)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($history->tgl_murojaah)->format('d-m-Y') }}</td>
                                            <td>{{ $history->surat->sura_name ?? '-' }}</td>
                                            <td>{{ $history->dariayat }}</td>
                                            <td>{{ $history->sampaiayat }}</td>
                                            <td>{{ $history->keterangan ?? '-' }}</td>
                                            <td>
                                                <a href="{{route('murojaahs.edit',$history->id)}}" class="btn btn-sm btn-info"><i class="far fa-edit"></i></a>
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
    
</div>


@endsection

@section('scripts')

<!-- SweetAlert2 Script -->
@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: "{{ session('success') }}",
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500
    });
</script>
@elseif (session('error'))
   <script>
       Swal.fire({
           icon: 'error',
           title: 'Oops...',
           text: "{{ session('error') }}",
           position: 'top-end',
           showConfirmButton: false,
           timer: 1500
       });
   </script>
@endif

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
</script>
@endsection