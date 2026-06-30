@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Welcome {{ Auth::user()->name }}
            </h2>
          </div>
          <!-- Page title actions -->
          <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
              <div id="real-date" style="font-size: 1.2rem;">
                  {{ date('l, d F Y') }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-deck row-cards">
          <div class="col-12">
            <div class="row row-cards">
              <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-primary text-white avatar">
                          <i class="fas fa-users" style="font-size: 24px;"></i>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Jamaah
                        </div>
                        <div class="text-muted">
                          123
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-success text-white avatar">
                          <i class="fas fa-users" style="font-size: 24px;"></i>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Agent
                        </div>
                        <div class="text-muted">
                          123
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-danger text-white avatar">
                          <i class="fa-solid fa-plane" style="font-size: 24px;"></i>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Paket Aktif
                        </div>
                        <div class="text-muted">
                          234
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-warning text-white avatar">
                          <i class="fa-solid fa-hand-holding-dollar" style="font-size: 24px;"></i>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          Jamaah Nabung
                        </div>
                        <div class="text-muted">
                          345
                        </div>
                      </div>
                    </div>
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

</script>
@endsection