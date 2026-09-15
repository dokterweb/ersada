<?php

use App\Http\Controllers\AkadController;
use App\Http\Controllers\AngsuranController;
use App\Http\Controllers\ApprovalSurveyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiskonDendaController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\OperasionalController;
use App\Http\Controllers\PelunasanController;
use App\Http\Controllers\PembayaranAngsuranController;
use App\Http\Controllers\PembiayaanController;
use App\Http\Controllers\PencairanController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});

Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'handleLogin']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
// FORM GANTI PASSWORD
Route::get('/password/change', [AuthController::class, 'changePasswordForm'])->middleware('auth')->name('password.change');
// PROSES UPDATE PASSWORD
Route::post('/password/update', [AuthController::class, 'updatePassword'])->middleware('auth')->name('password.update');

// Route untuk Admin
Route::middleware(['auth', 'role:superadmin'])->get('/superadmin/dashboard', [DashboardController::class, 'indexsuperadmin'])->name('superadmin.dashboard');
Route::middleware(['auth', 'role:marketing'])->get('/marketing/dashboard', [DashboardController::class, 'indexmarketing'])->name('marketing.dashboard');
Route::middleware(['auth', 'role:admincabang'])->get('/admincabang/dashboard', [DashboardController::class, 'indexadmincabang'])->name('admincabang.dashboard');

Route::get('/cabangs',[CabangController::class, 'index'])->middleware('role:superadmin')->name('cabangs');
Route::post('cabangs/store', [CabangController::class, 'store'])->middleware('role:superadmin')->name('cabangs.store');
Route::get('cabangs/{cabang}/edit', [CabangController::class, 'edit'])->middleware('role:superadmin')->name('cabangs.edit');
Route::put('cabangs/{cabang}', [CabangController::class, 'update'])->middleware('role:superadmin')->name('cabangs.update');

Route::get('/karyawans',[KaryawanController::class, 'index'])->middleware('role:superadmin')->name('karyawans');
Route::get('/karyawans/create',[KaryawanController::class, 'create'])->middleware('role:superadmin')->name('karyawans.create');
Route::post('karyawans/store', [KaryawanController::class, 'store'])->middleware('role:superadmin')->name('karyawans.store');
Route::get('karyawans/{karyawan}/edit', [KaryawanController::class, 'edit'])->middleware('role:superadmin')->name('karyawans.edit');
Route::put('karyawans/{karyawan}', [KaryawanController::class, 'update'])->middleware('role:superadmin')->name('karyawans.update');
Route::get('karyawans/{karyawan}',[KaryawanController::class, 'show'])->middleware('role:superadmin')->name('karyawans.show');
Route::delete('karyawans/{karyawan}',[KaryawanController::class, 'destroy'])->middleware('role:superadmin')
->name('karyawans.destroy');


Route::prefix('pengajuans')->middleware('role:marketing|spvmarketing|admincabang')->group(function () {
    Route::get('/', [PengajuanController::class, 'index'])->name('pengajuan.index');
    // STEP 1 (CREATE NEW)
    // halaman create pertama kali
    Route::get('/create', [PengajuanController::class, 'createStep1'])->name('pengajuan.create');
    // simpan pengajuan baru
    Route::post('/create', [PengajuanController::class, 'storeStep1'])->name('pengajuan.store');
    // STEP 1 (EDIT EXISTING)
    // kembali ke step 1 untuk edit
    Route::get('/{pengajuan}/step1', [PengajuanController::class, 'editStep1'])->name('pengajuan.step1');
    // update step 1
    Route::post('/{pengajuan}/step1', [PengajuanController::class, 'updateStep1'])->name('pengajuan.updateStep1');

    Route::get('/{pengajuan}/step2', [PengajuanController::class, 'step2'])->name('pengajuan.step2');
    Route::post('/{pengajuan}/step2', [PengajuanController::class, 'storeStep2'])->name('pengajuan.storeStep2');
    
    Route::get('/{pengajuan}/step3', [PengajuanController::class, 'step3'])->name('pengajuan.step3');
    Route::post('/{pengajuan}/step3', [PengajuanController::class, 'storeStep3'])->name('pengajuan.storeStep3');

    Route::get('/{pengajuan}/step4', [PengajuanController::class, 'step4'])->name('pengajuan.step4');
    Route::post('/{pengajuan}/step4', [PengajuanController::class, 'storeStep4'])->name('pengajuan.storeStep4');

    Route::get('/{pengajuan}/reviewdata', [PengajuanController::class, 'reviewData'])->name('pengajuan.reviewData');
    Route::post('/{pengajuan}/submit', [PengajuanController::class, 'submit'])->name('pengajuan.submit');

    Route::get('/{pengajuan}/analisa',[PengajuanController::class, 'analisa'])->name('pengajuan.analisa');
    Route::post('/{pengajuan}/analisa',[PengajuanController::class, 'storeAnalisa'])->name('pengajuan.storeAnalisa');

    Route::get('/{pengajuan}/jaminan',[PengajuanController::class,'jaminan'])->name('pengajuan.jaminan');
    Route::post('/{pengajuan}/jaminan',[PengajuanController::class,'storeJaminan'])->name('pengajuan.storeJaminan');

    Route::get('/{pengajuan}/kapital',[PengajuanController::class,'kapital'])->name('pengajuan.kapital');
    Route::post('/{pengajuan}/kapital',[PengajuanController::class,'storeKapital'])->name('pengajuan.storeKapital');
    Route::get('/{pengajuan}/review-final',[PengajuanController::class, 'reviewFinal'])->name('pengajuan.reviewFinal');
    Route::post('/{pengajuan}/review-final/save',[PengajuanController::class, 'saveReviewFinal'])->name('pengajuan.reviewFinal.save');
    Route::post('/{pengajuan}/review-final/submit',[PengajuanController::class, 'submitReviewFinal'])->name('pengajuan.reviewFinal.submit');
    Route::post('/{pengajuan}/mulai-revisi',[PengajuanController::class, 'mulaiRevisi'])->name('pengajuan.mulaiRevisi');
    Route::get('/{pengajuan}',[PengajuanController::class,'show'])->name('pengajuan.show');
});

Route::prefix('pimpinan')->middleware(['auth','role:komisaris|direktur|kacab'])
    ->group(function () {
        Route::get('/dashboard',[PimpinanController::class,'dashboard'])->name('pimpinan.dashboard');
        Route::get('/',[PimpinanController::class,'index'])->name('pimpinan.index');
        Route::get('/{pengajuan}',[PimpinanController::class,'show'])->name('pimpinan.show');
        // SURVEY - READ ONLY
        Route::get('/{pengajuan}/survey/berkas',[PimpinanController::class, 'surveyBerkas'])->name('pimpinan.survey.berkas');
        Route::get('/{pengajuan}/survey/dokumentasi',[PimpinanController::class, 'surveyDokumentasi'])->name('pimpinan.survey.dokumentasi');
        Route::get('/{pengajuan}/survey/review',[PimpinanController::class, 'surveyReview'])->name('pimpinan.survey.review');
        // APPROVAL PIMPINAN
        Route::post('/{pengajuan}/submit',[PimpinanController::class,'submit'])->name('pimpinan.submit');
    });

Route::middleware(['auth','role:spvsurveyor|surveyor'])->prefix('survey')->name('survey.')
    ->group(function(){
    Route::get('/', [SurveyController::class,'index'])->name('index');
    Route::get('/create/{pengajuan}', [SurveyController::class, 'create'])->name('create');
    Route::post('/store/{pengajuan}', [SurveyController::class, 'store'])->name('store');
    Route::get('/pengajuan/{pengajuan}',[SurveyController::class, 'showPengajuan'])->name('pengajuan.show');
    Route::post('/{survey}/accept',[SurveyController::class,'accept'])->name('accept');
    Route::post('/{survey}/start',[SurveyController::class,'start'])->name('start');
    Route::get('/{survey}/berkas', [SurveyController::class, 'stepBerkas'])->name('berkas');
    Route::post('/{survey}/berkas', [SurveyController::class, 'storeStepBerkas'])->name('storeBerkas');
    Route::get('/{survey}/dokumentasi',[SurveyController::class,'stepDokumentasi'])->name('dokumentasi');
    Route::post('/{survey}/upload',[SurveyController::class,'uploadDokumentasi'])->name('uploadDokumentasi');
    
    Route::post('/{survey}/review-check',[SurveyController::class,'checkDokumentasi'])->name('reviewCheck');
    Route::get('/{survey}/review',[SurveyController::class,'review'])->name('review');
    Route::post('/{survey}/submit',[SurveyController::class,'submit'])->name('submit');
    Route::get('/{survey}/review-spv', [SurveyController::class,'reviewSpv'])->name('reviewSpv');
    Route::post('/{survey}/review-spv', [SurveyController::class,'submitReviewSpv'])->name('submitReviewSpv');
});

Route::middleware(['auth','role:komisaris|direktur|kacab'])->prefix('approval-survey')->name('approvalSurvey.')
    ->group(function () {
        Route::get('/', [ApprovalSurveyController::class, 'index'])->name('index');
       Route::get('/{pengajuan}', [ApprovalSurveyController::class, 'show'])->name('show');
       Route::post('/{pengajuan}', [ApprovalSurveyController::class, 'store'])->name('store');
    });

    Route::middleware(['auth'])->prefix('pembiayaan')->name('pembiayaan.')
    ->group(function () {
        Route::get('/',[PembiayaanController::class,'index'])->name('index');
        Route::get('/create/{pengajuan}',[PembiayaanController::class,'create'])->name('create');
        Route::post('/store/{pengajuan}',[PembiayaanController::class,'store'])->name('store');
        Route::get('/{pembiayaan}/edit',[PembiayaanController::class,'edit'])->name('edit');
        Route::put('/{pembiayaan}',[PembiayaanController::class,'update'])->name('update');
        Route::get('/{pembiayaan}/review',[PembiayaanController::class,'review'])->name('review');
        // Route::post('/{pembiayaan}/review',[PembiayaanController::class,'submitReview'])->name('submitReview');
        Route::get('/{pembiayaan}/generate-jadwal',[PembiayaanController::class,'generateJadwal'])->name('generateJadwal');
        Route::post('/{pembiayaan}/generate-jadwal',[PembiayaanController::class,'storeJadwal'])->name('storeJadwal');
        Route::get('/{pembiayaan}/jadwal',[PembiayaanController::class,'jadwal'])->name('jadwal');
    });

    
    Route::prefix('akad')->name('akad.')->group(function(){
        Route::get('/create/{pembiayaan}',[AkadController::class,'create'])->name('create');
        Route::post('/store/{pembiayaan}',[AkadController::class,'store'])->name('store');
        
        Route::post('/{akad}/sign', [AkadController::class,'sign'])->name('sign');
        Route::get('/{akad}',[AkadController::class,'show'])->name('show');
        Route::get('/{akad}/edit',[AkadController::class,'edit'])->name('edit');
        Route::put('/{akad}',[AkadController::class,'update'])->name('update');
        Route::get('/{akad}/generate',[AkadController::class,'generate'])->name('generate');
        Route::get('/{akad}/download-word',[AkadController::class,'downloadWord'])->name('downloadWord');
    });


    Route::middleware(['auth'])->group(function () {
        Route::get('/pencairan', [PencairanController::class,'index'])->name('pencairan.index');
        Route::get('/akad/{akad}/pencairan/create', [PencairanController::class,'create'])->name('pencairan.create');
        Route::post('/akad/{akad}/pencairan', [PencairanController::class,'store'])->name('pencairan.store');
        Route::get('/pencairan/{pencairan}', [PencairanController::class,'show'])->name('pencairan.show');
    });


    Route::prefix('angsuran')->group(function () {
        Route::get('/', [AngsuranController::class,'index'])->name('angsuran.index');
        Route::get('/{pembiayaan}', [AngsuranController::class,'show'])->name('angsuran.show');
        Route::get('/{angsuran}/bayar', [AngsuranController::class,'create'])->name('angsuran.create');
        Route::get('/jadwal/{angsuran}/history',[AngsuranController::class, 'getHistory'])->name('angsuran.history');
        Route::get('/jadwal/{angsuran}/json',[AngsuranController::class,'getAngsuran'])->name('angsuran.json');
        Route::get('/{angsuran}/preview-denda',[AngsuranController::class, 'previewDenda'])->name('angsuran.previewDenda');
        Route::post('/jadwal/{angsuran}/bayar',[AngsuranController::class,'store'])->name('angsuran.store');
        Route::get('/pembayaran/{pembayaran}/cetak',[PembayaranAngsuranController::class,'cetak'])->name('pembayaran.cetak');
        Route::get('/angsuran/{angsuran}/history/cetak',[AngsuranController::class,'cetakHistory'])->name('angsuran.history.cetak');
    });

    Route::prefix('diskon-denda')->group(function () {
         // Kasir
        Route::get('/{angsuran}/create',[DiskonDendaController::class, 'create'])->name('diskon-denda.create');
        Route::post('/{angsuran}',[DiskonDendaController::class, 'store'])->name('diskon-denda.store');
          // Pimpinan
    Route::get('/approval',[DiskonDendaController::class, 'indexApproval'])->name('diskon-denda.approval');
        Route::post('/{pengajuan}/approve',[DiskonDendaController::class, 'approve'])->name('diskon-denda.approve');
        Route::post('/{pengajuan}/reject',[DiskonDendaController::class, 'reject'])->name('diskon-denda.reject');
    });

    Route::prefix('operasional')->middleware(['auth'])->name('operasional.')->group(function () {
        Route::get('/pembiayaan',[OperasionalController::class,'index'])->name('index');
        Route::get('/pembiayaan/{pembiayaan}',[OperasionalController::class,'show'])->name('show');
    });

    Route::prefix('pelunasan')->name('pelunasan.')->group(function () {
        Route::get('/{pembiayaan}/create', [PelunasanController::class,'create'])->name('create');
        Route::post('/{pembiayaan}', [PelunasanController::class,'store'])->name('store');
        Route::post('/{pelunasan}/approve',[PelunasanController::class, 'approve'])->name('approve');
        Route::post('/{pelunasan}/reject',[PelunasanController::class, 'reject'])->name('reject');
        Route::post('/{pelunasan}/bayar',[PelunasanController::class, 'bayar'])->name('bayar');
        Route::get('/{pelunasan}/cetak',[PelunasanController::class,'cetak'])->name('cetak');
        Route::get('/{pelunasan}', [PelunasanController::class,'show'])->name('show');
    });    

    Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/pembiayaan', [ReportController::class,'pembiayaan'])->name('pembiayaan');
        Route::get('/pencairan', [ReportController::class,'pencairan'])->name('pencairan');
        Route::get('/angsuran', [ReportController::class,'angsuran'])->name('angsuran');
        Route::get('/pelunasan', [ReportController::class,'pelunasan'])->name('pelunasan');
        Route::get('/outstanding', [ReportController::class,'outstanding'])->name('outstanding');
        Route::get('/jatuh-tempo', [ReportController::class,'jatuhTempo'])->name('jatuhTempo');
        Route::get('/npl', [ReportController::class,'npl'])->name('npl');
        Route::get('/pembiayaan/excel',[ReportController::class,'exportPembiayaanExcel'])->name('pembiayaan.excel');
        Route::get('/pembiayaan/pdf',[ReportController::class,'exportPembiayaanPdf'])->name('pembiayaan.pdf');
        Route::get('/pencairan/excel',[ReportController::class,'exportPencairanExcel'])->name('pencairan.excel');
        Route::get('/pencairan/pdf',[ReportController::class,'exportPencairanPdf'])->name('pencairan.pdf');
        Route::get('/angsuran/excel', [ReportController::class,'exportAngsuranExcel'])->name('angsuran.excel');
        Route::get('/angsuran/pdf',[ReportController::class,'exportAngsuranPdf'])->name('angsuran.pdf');
        Route::get('/pelunasan/excel', [ReportController::class,'exportPelunasanExcel'])->name('pelunasan.excel');
        Route::get('/pelunasan/pdf',[ReportController::class,'exportPelunasanPdf'])->name('pelunasan.pdf');
        Route::get('/outstanding/excel', [ReportController::class,'exportOutstandingExcel'])->name('outstanding.excel');
        Route::get('/outstanding/pdf',[ReportController::class,'exportOutstandingPdf'])->name('outstanding.pdf');
        Route::get('/jatuh-tempo/excel', [ReportController::class, 'exportJatuhTempoExcel'])->name('jatuhtempo.excel');
        Route::get('/jatuh-tempo/pdf', [ReportController::class, 'exportJatuhTempoPdf'])->name('jatuhtempo.pdf');
        Route::get('/npl/excel', [ReportController::class, 'exportNplExcel'])->name('npl.excel');
        Route::get('/npl/pdf', [ReportController::class, 'exportNplPdf'])->name('npl.pdf');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });    

