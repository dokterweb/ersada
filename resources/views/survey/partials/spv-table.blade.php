<div class="row row-cards">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-blue-lt">
                <strong>Pengajuan Menunggu Survey</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60">No</th>
                                <th>No Pengajuan</th>
                                <th>Nasabah</th>
                                <th>Marketing</th>
                                <th>Cabang</th>
                                <th>Tanggal</th>
                                <th width="220">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuans as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nomor_pengajuan }}</td>
                                <td>{{ optional($item->nasabah)->nama }}</td>
                                <td>{{ $item->marketing->user->name }}</td>
                                <td>{{ optional($item->cabang)->nama_cabang }}</td>
                                <td>{{ $item->created_at->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('survey.create',$item->id) }}" class="btn btn-success btn-sm"><i class="fa fa-play"></i>
                                        Proses Survey
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada pengajuan yang menunggu survey.
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

<div class="row row-cards mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-warning-lt">
                <div>
                    <h3 class="card-title">
                        Survey Saya
                    </h3>
                    <small class="text-muted">
                        Survey yang menjadi tanggung jawab Anda
                    </small>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-vcenter table-striped">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>No Pengajuan</th>
                                <th>Nasabah</th>
                                <th>Marketing</th>
                                <th>Cabang</th>
                                <th>Status</th>
                                <th width="170">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($surveySaya as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->pengajuan->nomor_pengajuan }}</td>
                                <td>{{ optional($item->pengajuan->nasabah)->nama }}</td>
                                <td>{{ optional($item->pengajuan->marketing->user)->name }}</td>
                                <td>{{ optional($item->pengajuan->cabang)->nama }}</td>
                                <td>
                                    @switch($item->status)
                                        @case('waiting')
                                            <span class="badge bg-warning">
                                                WAITING
                                            </span>
                                            @break
                                        @case('accepted')
                                            <span class="badge bg-info">
                                                ACCEPTED
                                            </span>
                                            @break
                                        @case('progress')
                                            <span class="badge bg-primary">
                                                PROGRESS
                                            </span>
                                            @break
                                        @case('revision')
                                            <span class="badge bg-danger">
                                                REVISION
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($item->status=='waiting')
                                        <form action="{{ route('survey.accept',$item) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-warning btn-sm">Terima</button>
                                        </form>
                                    @elseif($item->status=='accepted')
                                        <form action="{{ route('survey.start',$item) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-primary btn-sm">Mulai Survey</button>
                                        </form>
                                    @elseif($item->status=='progress')
                                        <a href="{{ route('survey.berkas',$item) }}" class="btn btn-success btn-sm">
                                            Lanjut Survey
                                        </a>
                                    @elseif($item->status=='revision')
                                        <a href="{{ route('survey.berkas',$item) }}" class="btn btn-danger btn-sm">
                                            Perbaiki
                                        </a>
                                    @endif
                                        <a href="{{ route('survey.pengajuan.show',$item->pengajuan_id) }}"
                                            class="btn btn-info btn-sm"target="_blank">Detail
                                        </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Belum ada survey yang menjadi tanggung jawab Anda.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($surveySaya->hasPages())
                    <div class="card-footer">
                        {{ $surveySaya->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="row row-cards mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-success-lt">
                <strong>
                    Hasil Survey Masuk
                </strong>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Pengajuan</th>
                                <th>Nasabah</th>
                                <th>Surveyor</th>
                                <th>Tanggal Submit</th>
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
                                <td>{{ optional($survey->assignedTo)->name }}</td>
                                <td>{{ optional($survey->submitted_at)?->format('d-m-Y H:i') }}</td>
                                <td><span class="badge bg-success">Submitted</span></td>
                                <td>
                                    <a href="{{ route('survey.reviewSpv',$survey) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-search"></i>Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Belum ada hasil survey.
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