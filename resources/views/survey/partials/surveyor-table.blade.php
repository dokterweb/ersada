<div class="row row-cards">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-blue-lt">
                <strong>Tugas Survey Saya</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60">No</th>
                                <th>No Pengajuan</th>
                                <th>Nasabah</th>
                                <th>Status</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
    
                        <tbody>
                            @forelse($surveys as $survey)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $survey->pengajuan->nomor_pengajuan }}</td>
                                <td>{{ optional($survey->pengajuan->nasabah)->nama }}</td>
                                <td>
                                    @switch($survey->status)
                                        @case('waiting')
                                            <span class="badge bg-warning">Menunggu Diterima</span>
                                            @break
                                        @case('accepted')
                                            <span class="badge bg-info">Diterima</span>
                                            @break
                                        @case('progress')
                                            <span class="badge bg-primary">Sedang Survey</span>
                                            @break
                                        @case('finished')
                                            <span class="badge bg-success">Selesai</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($survey->status=='waiting')
                                        <form action="{{ route('survey.accept',$survey->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-success btn-sm"><i class="fa fa-check"></i>
                                                Terima
                                            </button>
                                        </form>
                                    @elseif($survey->status=='accepted')
                                        <form action="{{ route('survey.start',$survey->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-primary btn-sm"><i class="fa fa-play"></i>
                                                Mulai Survey
                                            </button>
                                        </form>
                                    @elseif($survey->status=='progress')
                                        <a href="#" class="btn btn-warning btn-sm">
                                            Lanjut Survey
                                        </a>
                                    @else
                                        <a href="#" class="btn btn-secondary btn-sm">
                                            Lihat
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Tidak ada tugas survey.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>