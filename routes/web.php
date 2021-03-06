<?php

use App\Http\Controllers\DomainChecksController;
use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', fn() => view('main'))->name('home');

Route::resource('urls', UrlController::class);

Route::post('urls/{url}/checks', [UrlController::class, 'storeCheck'])->name('urls.storeCheck');
