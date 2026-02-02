<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\UstadzController;
use App\Http\Controllers\KelasnyaController;
use App\Http\Controllers\KelompokController;
use App\Http\Controllers\MurojaahController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HariliburController;
use App\Http\Controllers\AbsensiSantriController;
use App\Http\Controllers\AbsensiUstadzController;

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
Route::middleware(['auth', 'role:admin'])->get('/admin/dashboard', [DashboardController::class, 'indexadmin'])->name('admin.dashboard');

// Route untuk Ustadz
Route::middleware(['auth', 'role:ustadz'])->get('/ustadz/dashboard', [DashboardController::class, 'indexustadz'])->name('ustadz.dashboard');

// Route untuk Siswa
Route::middleware(['auth', 'role:santri'])->get('/santri/dashboard', [DashboardController::class, 'indexsantri'])->name('santri.dashboard');

Route::get('/kelasnyas',[KelasnyaController::class, 'index'])->middleware('role:admin')->name('kelasnyas');
Route::post('kelasnyas/store', [KelasnyaController::class, 'store'])->middleware('role:admin')->name('kelasnyas.store');
Route::get('kelasnyas/{kelasnya}/edit', [KelasnyaController::class, 'edit'])->middleware('role:admin')->name('kelasnyas.edit');
Route::put('kelasnyas/{kelasnya}', [KelasnyaController::class, 'update'])->middleware('role:admin')->name('kelasnyas.update');
Route::delete('kelasnyas/{kelasnya}', [KelasnyaController::class, 'destroy'])->middleware('role:admin')->name('kelasnyas.destroy');

Route::get('/kelompoks',[KelompokController::class, 'index'])->middleware('role:admin')->name('kelompoks');
Route::post('kelompoks/store', [KelompokController::class, 'store'])->middleware('role:admin')->name('kelompoks.store');
Route::get('kelompoks/{kelompok}/edit', [KelompokController::class, 'edit'])->middleware('role:admin')->name('kelompoks.edit');
Route::put('kelompoks/{kelompok}', [KelompokController::class, 'update'])->middleware('role:admin')->name('kelompoks.update');
Route::delete('kelompoks/{kelasnya}', [KelompokController::class, 'destroy'])->middleware('role:admin')->name('kelompoks.destroy');

Route::get('/ustadzs',[UstadzController::class, 'index'])->middleware('role:admin')->name('ustadzs');
Route::post('ustadzs/store', [UstadzController::class, 'store'])->middleware('role:admin')->name('ustadzs.store');
Route::get('ustadzs/{ustadz}/edit', [UstadzController::class, 'edit'])->middleware('role:admin')->name('ustadzs.edit');
Route::put('ustadzs/{ustadz}', [UstadzController::class, 'update'])->middleware('role:admin')->name('ustadzs.update');
Route::get('ustadzs/{ustadz}', [UstadzController::class, 'show'])->middleware('role:admin')->name('ustadzs.show');
Route::delete('ustadzs/{ustadz}', [UstadzController::class, 'destroy'])->middleware('role:admin')->name('ustadzs.destroy');

Route::get('/santris',[SantriController::class, 'index'])->middleware('role:admin')->name('santris');
Route::post('santris/store', [SantriController::class, 'store'])->middleware('role:admin')->name('santris.store');
Route::get('santris/{santri}/edit', [SantriController::class, 'edit'])->middleware('role:admin')->name('santris.edit');
Route::put('santris/{santri}', [SantriController::class, 'update'])->middleware('role:admin')->name('santris.update');
Route::get('santris/{santri}', [SantriController::class, 'show'])->middleware('role:admin')->name('santris.show');
Route::delete('santris/{santri}', [SantriController::class, 'destroy'])->middleware('role:admin')->name('santris.destroy');

Route::get('/murojaahs',[MurojaahController::class, 'index'])->middleware('role:admin|ustadz')->name('murojaahs');
Route::get('/murojaahs/create',[MurojaahController::class, 'create'])->middleware('role:admin|ustadz')->name('murojaahs.create');
    // ajax: ambil siswa per kelompok
Route::get('/api/kelompoks/{kelompok}/santris',[MurojaahController::class,'getSantrisByKelompok'])
    ->middleware('role:admin|ustadz')->name('api.kelompok.santris');
     // ajax: (opsional) detail surat (kalau mau isi No/Juz/Hal otomatis)
Route::get('/api/madina/{sura_no}',[MurojaahController::class,'getSuratDetails'])
     ->middleware('role:admin|ustadz')->name('api.madina.details');
Route::get('/api/santris/{santri}/murojaah', [MurojaahController::class, 'getMurojaahBySantri'])
    ->middleware('role:admin|ustadz')->name('api.santri.murojaah');
Route::post('/murojaahs/store', [MurojaahController::class, 'store'])->middleware('role:admin|ustadz')->name('murojaahs.store');
Route::get('/murojaahs/{id}', [MurojaahController::class, 'show'])->middleware('role:admin|ustadz')->name('murojaahs.show');
Route::get('/murojaahs/{id}/edit', [MurojaahController::class, 'edit'])->middleware('role:admin|ustadz')->name('murojaahs.edit');
Route::put('/murojaahs/{id}/update', [MurojaahController::class, 'update'])->middleware('role:admin|ustadz')->name('murojaahs.update');
Route::get('/rekap-murojaah', [MurojaahController::class, 'rekap'])
->middleware('role:admin|ustadz')->name('murojaahs.rekap');

Route::get('/absensi-santri', [AbsensiSantriController::class, 'index'])->name('absensisantris.index');
Route::get('/absensi-santri/create', [AbsensiSantriController::class, 'create'])->name('absensisantris.create');
Route::post('/absensi-santri', [AbsensiSantriController::class, 'store'])->name('absensisantris.store');
Route::get('/absensi-santri/get-santri/{kelompok}', [AbsensiSantriController::class, 'getSantri'])->name('absensisantris.get-santri');
Route::get('/absensi-santri/{id}/edit', [AbsensiSantriController::class, 'edit'])->name('absensisantris.edit');
Route::put('/absensi-santri/{id}', [AbsensiSantriController::class, 'update'])->name('absensisantris.update');
Route::get('/rekap-absensi', [AbsensiSantriController::class, 'rekapabsen'])->name('absensisantris.rekapabsen');
Route::get('/rekap-absensi-santri', [AbsensiSantriController::class, 'rekapabsensantri'])->name('absensisantris.rekapabsensantri');

Route::get('/absensi-ustadz', [AbsensiUstadzController::class, 'index'])->name('absensiustadzs.index');
Route::get('/absensi-ustadz/{id}/edit', [AbsensiUstadzController::class, 'edit'])->name('absensiustadzs.edit');
Route::put('/absensi-ustadz/{id}', [AbsensiUstadzController::class, 'update'])->name('absensiustadzs.update');
Route::get('/rekap-absensi-ustadz', [AbsensiUstadzController::class, 'rekapabsenustadz'])->name('absensiustadzs.rekapabsenustadz');

Route::get('/hariliburs',[HariliburController::class, 'index'])->middleware('role:admin')->name('hariliburs');
Route::get('/hariliburs/create',[HariliburController::class, 'create'])->middleware('role:admin')->name('hariliburs.create'); 
Route::post('hariliburs/store', [HariliburController::class, 'store'])->middleware('role:admin')->name('hariliburs.store');
Route::get('/hariliburs/{id}/edit', [HariliburController::class, 'edit'])->middleware('role:admin')->name('hariliburs.edit');
Route::put('/hariliburs/{id}', [HariliburController::class, 'update'])->middleware('role:admin')->name('hariliburs.update');
Route::get('hariliburs/bulanan', [HariliburController::class, 'createMonthly'])->name('hariliburs.monthly');
Route::post('hariliburs/bulanan', [HariliburController::class, 'storeMonthly'])->name('hariliburs.monthly.store');
Route::delete('/hariliburs/{id}/destroy', [HariliburController::class, 'destroy'])->middleware('role:admin')->name('hariliburs.destroy');