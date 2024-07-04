<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\Teacher\Assessments;
use App\Http\Controllers\PenilaianTkController;
use App\Http\Controllers\KMSemesterRaportController;
use App\Http\Controllers\KMMidSemesterRaportController;
use App\Http\Controllers\P5BKPancasilaRaportController;
use App\Http\Controllers\PgKgRaportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/login', '/admin/login')->name('login');

// Rute Fallback untuk 404
Route::fallback(function () {
    $title = 'Page Not Found';
    return view('404', compact('title'));
});

Route::get('/404', function () {
    return view('404');
})->name('404');

// routes/web.php

Route::get('/403', function () {
    return view('403');
})->name('403');


Route::get('/preview-pancasila-raport', [P5BKPancasilaRaportController::class, 'previewPancasilaRaport'])->name('preview-pancasila-raport');
Route::get('/preview-data-raport', [KMSemesterRaportController::class, 'previewDataRaport'])->name('preview-data-raport');
Route::get('/preview-semester-raport', [KMSemesterRaportController::class, 'previewSemesterRaport'])->name('preview-semester-raport');
Route::get('/preview-mid-semester-raport', [KMMidSemesterRaportController::class, 'previewMidSemesterRaport'])->name('preview-mid-semester-raport');
Route::get('/preview-pg-kg-raport', [PgKgRaportController::class, 'previewPgKgRaport'])->name('preview-pg-kg-raport');
Route::get('/preview-data-pg-kg-raport', [PgKgRaportController::class, 'previewDataPgKgRaport'])->name('preview-data-pg-kg-raport');
