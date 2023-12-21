<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
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

Route::get('/', function () {
    if (session()->get('user') == null) return redirect()->route('form-login');
    return redirect()->route('home');
});

Route::controller(EventController::class)->group(function () {
    Route::get('/', 'showCalendar')->name('home');
    Route::get('/events-by-date', 'getEventsByDate')->name('event-by-date');
    Route::get('/detail-event', 'getDetailEvent')->name('detail-event');
    Route::get('/add-event', 'showFormAddEvent')->name('show-add-event');
    Route::post('/add-event', 'storeEvent')->name('store-event');
    Route::get('/edit-event', 'showFormEditEvent')->name('show-edit-event');
    Route::post('/edit-event', 'editEvent')->name('edit-event');
    Route::delete('/delete-event', 'deleteEvent')->name('delete-event');
    Route::get('/print-pdf', 'showPrintPdf')->name('show-print-pdf');
});

Route::controller(UserController::class)->group(function () {
    Route::post('/login', 'doLogin')->name('do-login');
    Route::get('/login', 'formLogin')->name('form-login');
});
