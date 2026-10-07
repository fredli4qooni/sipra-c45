<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;

// Landing / Root redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Gateway Route (redirects by role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Settings & Help Center
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.edit');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
    Route::get('/bantuan', [DashboardController::class, 'bantuan'])->name('bantuan');

    // 1. Admin Akademik Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

        // Master Data Mahasiswa
        Route::resource('mahasiswa', \App\Http\Controllers\MahasiswaController::class);

        // Master Data Dosen Pembimbing Akademik (Dosen PA)
        Route::resource('dosen', \App\Http\Controllers\DosenPaController::class);

        // Master Data Pengaturan Kategori & Teks Peringatan Risiko
        Route::post('risiko/{risiko}/reset', [\App\Http\Controllers\KategoriRisikoController::class, 'reset'])->name('risiko.reset');
        Route::resource('risiko', \App\Http\Controllers\KategoriRisikoController::class)->except(['create', 'store', 'destroy']);

        // Master Data Akademik & Excel Import/Export
        Route::get('akademik/template', [\App\Http\Controllers\DataAkademikController::class, 'downloadTemplate'])->name('akademik.template');
        Route::post('akademik/import', [\App\Http\Controllers\DataAkademikController::class, 'import'])->name('akademik.import');
        Route::get('akademik/export', [\App\Http\Controllers\DataAkademikController::class, 'export'])->name('akademik.export');
        Route::resource('akademik', \App\Http\Controllers\DataAkademikController::class);

        // Core Engine Data Mining C4.5
        Route::post('c45/{c45}/activate', [\App\Http\Controllers\C45Controller::class, 'activate'])->name('c45.activate');
        Route::resource('c45', \App\Http\Controllers\C45Controller::class);

        // Visual Tree & Rules Explorer
        Route::get('tree/{c45?}', [\App\Http\Controllers\DecisionTreeVisualizerController::class, 'showTree'])->name('tree.show');
        Route::get('rules/{c45?}', [\App\Http\Controllers\DecisionTreeVisualizerController::class, 'showRules'])->name('rules.index');

        // Modul Prediksi Risiko C4.5
        Route::get('prediksi', [\App\Http\Controllers\PrediksiController::class, 'index'])->name('prediksi.index');
        Route::get('prediksi/single', [\App\Http\Controllers\PrediksiController::class, 'createSingle'])->name('prediksi.single');
        Route::post('prediksi/single', [\App\Http\Controllers\PrediksiController::class, 'storeSingle'])->name('prediksi.single.store');
        Route::get('prediksi/batch', [\App\Http\Controllers\PrediksiController::class, 'createBatch'])->name('prediksi.batch');
        Route::get('prediksi/batch-template', [\App\Http\Controllers\PrediksiController::class, 'downloadBatchTemplate'])->name('prediksi.batch.template');
        Route::post('prediksi/batch', [\App\Http\Controllers\PrediksiController::class, 'storeBatch'])->name('prediksi.batch.store');
        Route::get('prediksi/batch/{batch}', [\App\Http\Controllers\PrediksiController::class, 'showBatch'])->name('prediksi.batch.show');
        Route::get('prediksi/batch/{batch}/export', [\App\Http\Controllers\PrediksiController::class, 'exportBatch'])->name('prediksi.batch.export');
        Route::get('prediksi/{prediksi}', [\App\Http\Controllers\PrediksiController::class, 'show'])->name('prediksi.show');

        // Early Warning System (EWS)
        Route::get('ews', [\App\Http\Controllers\EwsController::class, 'index'])->name('ews.index');
        Route::put('ews/{dataAkademik}/intervensi', [\App\Http\Controllers\EwsController::class, 'updateIntervention'])->name('ews.intervensi.update');

        // Modul Laporan & Rekapitulasi
        Route::get('laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/print', [\App\Http\Controllers\LaporanController::class, 'print'])->name('laporan.print');
        Route::get('laporan/export', [\App\Http\Controllers\LaporanController::class, 'exportExcel'])->name('laporan.export');
    });

    // 2. Pihak Prodi / Dosen PA Routes
    Route::middleware(['role:admin,prodi,dosen_pa'])->prefix('prodi')->name('prodi.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'prodiDashboard'])->name('dashboard');

        // Read-only model view for Prodi / Dosen PA
        Route::get('c45', [\App\Http\Controllers\C45Controller::class, 'index'])->name('c45.index');
        Route::get('c45/{c45}', [\App\Http\Controllers\C45Controller::class, 'show'])->name('c45.show');

        // Tree & Rules Explorer
        Route::get('tree/{c45?}', [\App\Http\Controllers\DecisionTreeVisualizerController::class, 'showTree'])->name('tree.show');
        Route::get('rules/{c45?}', [\App\Http\Controllers\DecisionTreeVisualizerController::class, 'showRules'])->name('rules.index');

        // Prediksi & Monitoring
        Route::get('prediksi', [\App\Http\Controllers\PrediksiController::class, 'index'])->name('prediksi.index');
        Route::get('prediksi/single', [\App\Http\Controllers\PrediksiController::class, 'createSingle'])->name('prediksi.single');
        Route::post('prediksi/single', [\App\Http\Controllers\PrediksiController::class, 'storeSingle'])->name('prediksi.single.store');
        Route::get('prediksi/batch', [\App\Http\Controllers\PrediksiController::class, 'createBatch'])->name('prediksi.batch');
        Route::get('prediksi/batch-template', [\App\Http\Controllers\PrediksiController::class, 'downloadBatchTemplate'])->name('prediksi.batch.template');
        Route::post('prediksi/batch', [\App\Http\Controllers\PrediksiController::class, 'storeBatch'])->name('prediksi.batch.store');
        Route::get('prediksi/batch/{batch}', [\App\Http\Controllers\PrediksiController::class, 'showBatch'])->name('prediksi.batch.show');
        Route::get('prediksi/batch/{batch}/export', [\App\Http\Controllers\PrediksiController::class, 'exportBatch'])->name('prediksi.batch.export');
        Route::get('prediksi/{prediksi}', [\App\Http\Controllers\PrediksiController::class, 'show'])->name('prediksi.show');

        // Early Warning System (EWS)
        Route::get('ews', [\App\Http\Controllers\EwsController::class, 'index'])->name('ews.index');
        Route::put('ews/{dataAkademik}/intervensi', [\App\Http\Controllers\EwsController::class, 'updateIntervention'])->name('ews.intervensi.update');

        // Modul Laporan & Rekapitulasi
        Route::get('laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/print', [\App\Http\Controllers\LaporanController::class, 'print'])->name('laporan.print');
        Route::get('laporan/export', [\App\Http\Controllers\LaporanController::class, 'exportExcel'])->name('laporan.export');
    });

    // 3. Mahasiswa Portal Routes
    Route::middleware(['role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'mahasiswaDashboard'])->name('dashboard');
    });
});
