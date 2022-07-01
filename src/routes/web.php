<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/settings/roles', [RoleController::class, 'index']);
Route::get('/settings/roles/new', [RoleController::class, 'create']);
Route::post('/settings/roles/new', [RoleController::class, 'store']);
Route::get('/settings/roles/delete/{id}', [RoleController::class, 'showDelete']);
Route::delete('/settings/roles/delete/{id}', [RoleController::class, 'delete']);
Route::get('/settings/roles/{id}', [RoleController::class, 'edit']);
Route::put('/settings/roles/{id}', [RoleController::class, 'update']);

// Settings
Route::get('/settings/{category}', [SettingController::class, 'category'])->name('settings.category');
