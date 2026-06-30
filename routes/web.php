<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PengajuanController;
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


Route::prefix('pengajuans')->middleware('role:marketing')->group(function () {

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

    Route::get('/{pengajuan}/review', [PengajuanController::class, 'review'])->name('pengajuan.review');
    Route::post('/{pengajuan}/submit', [PengajuanController::class, 'submit'])->name('pengajuan.submit');

    Route::get('/{pengajuan}/analisa',[PengajuanController::class, 'analisa'])->name('pengajuan.analisa');
    Route::post('/{pengajuan}/analisa',[PengajuanController::class, 'storeAnalisa'])->name('pengajuan.storeAnalisa');

    Route::get('/{pengajuan}/jaminan',[PengajuanController::class,'jaminan'])->name('pengajuan.jaminan');
    Route::post('/{pengajuan}/jaminan',[PengajuanController::class,'storeJaminan'])->name('pengajuan.storeJaminan');

    Route::get('/{pengajuan}/kapital',[PengajuanController::class,'kapital'])->name('pengajuan.kapital');
    Route::post('/{pengajuan}/kapital',[PengajuanController::class,'storeKapital'])->name('pengajuan.storeKapital');
});