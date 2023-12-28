<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsappController;
use Illuminate\Support\Facades\Route;

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
// Route Untuk Ke halaman utama
Route::get('/', function () {
    // Jika belum pernah login maka akan diarahkan ke route halaman login
    if (session()->get('user') == null) return redirect()->route('form-login');
    // Jika sudah pernah login maka akan diarahkan ke route halaman utama aplikasi
    return redirect()->route('home');
});


Route::controller(WhatsappController::class)->group(function () {
    Route::get('/whatsapp', 'index')->name('whatsapp.index');
    Route::post('/whatsapp', 'store')->name('whatsapp.store');
    Route::post('/whatsapp/delete_whatsapp', 'destroy')->name('whatsapp.destroy');
    Route::post('/whatsapp/send-event-to-whatsapp', 'sendEventToWhatsapp');
});

// Route yang menggunakan controller untuk mengelola Fitur Agenda Acara
Route::controller(EventController::class)->group(function () {
    // Route untuk menampilkan halaman utama
    Route::get('/', 'showCalendar')->name('home');
    // Route untuk mengembalikan nilai detail acara berdasarkan id acara
    Route::get('/detail-event', 'getDetailEvent')->name('detail-event');
    // Route untuk mengembalikan nilai kumpulan acara berdasarkan tanggal
    Route::get('/events-by-date', 'getEventsByDate')->name('event-by-date');
    // Route untuk melakukan hapus data acara berdasarkan id
    Route::delete('/delete-event', 'deleteEvent')->name('delete-event');

    // Route untuk menampilkan halaman tambah acara
    Route::get('/add-event', 'showFormAddEvent')->name('show-add-event');
    // Route untuk menambah data acara
    Route::post('/add-event', 'storeEvent')->name('store-event');

    // Route untuk menampilkan halaman edit acara
    Route::get('/edit-event', 'showFormEditEvent')->name('show-edit-event');
    // Route untuk edit data acara
    Route::post('/edit-event', 'editEvent')->name('edit-event');

    // Route untuk menampilkan halaman print pdf
    Route::get('/print-pdf', 'showPrintPdf')->name('show-print-pdf');
    // Route untuk menampilkan halaman unduh print pdf
    Route::get('/print-pdf/unduh', 'downloadPdf')->name('download-pdf');
    // Route untuk mendapatkan acara pada rentang dua tanggal
    Route::get('/events-by-data-range', 'getEventByDateRange')->name('get-event-by-date-range');
});

// Route yang menggunakan controller untuk mengelola Fitur Akun
Route::controller(UserController::class)->group(function () {
    // Route untuk memproses login
    Route::post('/login', 'doLogin')->name('do-login');
    // Route untuk menampilkan halaman login
    Route::get('/login', 'formLogin')->name('form-login');
});
