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
                                <i class="fas fa-wallet"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                {{ $totalAktif }}
                            </div>
                            <div class="text-muted">
                            Total Aktif
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
                                <span class="bg-cyan text-white avatar">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">Rp {{ number_format($pembayaranBulanIni,0,',','.') }}</div>
                                <div class="text-muted">Pembayaran Bulan ini</div>
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
                              <span class="bg-facebook text-white avatar">
                                  <i class="fas fa-exclamation-circle"></i>
                              </span>
                            </div>
                            <div class="col">
                              <div class="font-weight-medium">{{ $menunggak }}</div>
                              <div class="text-muted">Menunggak</div>
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
                                <span class="bg-yellow avatar">
                                    <i class="fas fa-check"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">{{ $totalLunas }}</div>
                                <div class="text-muted">Total Lunas</div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
       
        <div class="row row-cards mt-3">
          {{-- Outstanding per Cabang --}}
          <div class="col-lg-6">
              <div class="card">
                  <div class="card-header">
                      <h3 class="card-title">
                          Outstanding per Cabang
                      </h3>
                  </div>
                  <div class="table-responsive">
                      <table class="table table-vcenter card-table">
                          <thead>
                              <tr>
                                  <th>Cabang</th>
                                  <th class="text-end">Outstanding</th>
                              </tr>
                          </thead>
                          <tbody>
                              @forelse($outstandingCabang as $item)
                              <tr>
                                  <td>{{ $item->nama_cabang }}</td>
                                  <td class="text-end">
                                      <strong>Rp {{ number_format($item->outstanding,0,',','.') }}</strong>
                                  </td>
                              </tr>
                              @empty
                              <tr>
                                  <td colspan="2" class="text-center">Belum ada data</td>
                              </tr>
                              @endforelse
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      
          {{-- Outstanding per Marketing --}}
          <div class="col-lg-6">
              <div class="card">
                  <div class="card-header">
                      <h3 class="card-title">
                          Outstanding per Marketing
                      </h3>
                  </div>
                  <div class="table-responsive">
                      <table class="table table-vcenter card-table">
                          <thead>
                              <tr>
                                  <th>Marketing</th>
                                  <th class="text-end">
                                      Outstanding
                                  </th>
                              </tr>
                          </thead>
                          <tbody>
                              @forelse($outstandingMarketing as $item)
                              <tr>
                                  <td>
                                      {{ $item->name }}
                                  </td>
                                  <td class="text-end">
                                      <strong>
                                          Rp {{ number_format($item->outstanding,0,',','.') }}
                                      </strong>
                                  </td>
                              </tr>
                              @empty
                              <tr>
                                  <td colspan="2" class="text-center">
                                      Belum ada data
                                  </td>
                              </tr>
                              @endforelse
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>

        <div class="row mt-4">
          <div class="col-md-12">
              <div class="card">
                  <div class="card-header bg-primary">
                      Grafik Pembayaran Bulanan
                  </div>
                  <div class="card-body">
                      <canvas id="chartPembayaran" height="90"></canvas>
                  </div>
              </div>
          </div>
      </div>
      
      <div class="row mt-4">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header bg-success">
                Grafik Pencairan Bulanan
            </div>
            <div class="card-body">
                <canvas id="chartPencairan"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card">
            <div class="card-header bg-danger">
                Grafik Pelunasan Bulanan
            </div>
            <div class="card-body">
                <canvas id="chartPelunasan"></canvas>
            </div>
          </div>
        </div>
      </div>
      
    </div>
    
</div>
@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

  const bulan = [
  'Jan','Feb','Mar','Apr','Mei','Jun',
  'Jul','Agu','Sep','Okt','Nov','Des'
  ];

  const pembayaran = Array(12).fill(0);
  @foreach($pembayaranBulanan as $item)
    pembayaran[{{ $item->bulan-1 }}]={{ $item->total }};
  @endforeach
  new Chart(document.getElementById('chartPembayaran'),{
    type:'line',
    data:{
      labels:bulan,
      datasets:[{
        label:'Pembayaran',
        data:pembayaran,
        fill:true,
        tension:.4
      }]
    }
  });

  const pencairan = Array(12).fill(0);
  @foreach($pencairanBulanan as $item)
    pencairan[{{ $item->bulan-1 }}]={{ $item->total }};
  @endforeach
  new Chart(document.getElementById('chartPencairan'),{
    type:'bar',
    data:{
      labels:bulan,
      datasets:[{
        label:'Pencairan',
        data:pencairan
      }]
    }
  });

  const pelunasan = Array(12).fill(0);
  @foreach($pelunasanBulanan as $item)
    pelunasan[{{ $item->bulan-1 }}]={{ $item->total }};
  @endforeach
  new Chart(document.getElementById('chartPelunasan'),{
    type:'bar',
    data:{
      labels:bulan,
      datasets:[{
        label:'Pelunasan',
        data:pelunasan
      }]
    }
  });

</script>
@endsection