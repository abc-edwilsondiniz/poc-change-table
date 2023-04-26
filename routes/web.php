<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\ProdutoController;

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

Route::get('/tracking', [TrackingController::class, 'index']);
Route::get('/tracking-estoque', [EstoqueController::class, 'estoque']);
Route::get('/tracking-produto', [ProdutoController::class, 'produto']);

Route::get('/', function () {
    return view('welcome');
});
