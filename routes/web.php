<?php

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
    return view('pages.index');
})->name('home');

Route::controller(UserController::class)->group(function () {
    Route::post('/login', 'doLogin')->name('do-login');
    Route::get('/login', 'formLogin')->name('form-login');
});
