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
                            <h3 class="card-title">Data Murojaah</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive p-3">
                                <table id="mytable" class="table table-vcenter card-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Siswa</th>
                                        <th>Nama Ustadz</th>
                                        <th>Kelompok</th>
                                        <th>Kelas</th>
                                        <th>Kelamin</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($santris as $s)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $s->user->name }}</td>
                                            <td>{{ optional(optional($s->kelompok)->ustadz)->user->name ?? '-' }}</td>
                                            <td>{{$s->kelompok->nama_kelompok}} </td>
                                            <td>{{$s->kelasnya->nama_kelas}} </td>
                                            <td>{{$s->kelamin}} </td>
                                            <td>
                                                <a href="{{route('murojaahs.show',$s->id)}}" class="btn btn-sm btn-info"><i class="far fa-edit"></i></a>
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