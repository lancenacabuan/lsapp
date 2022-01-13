<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PostsController;

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

/*
Route::get('/users/{id}/{name}/', function ($id, $name) {
    return 'This is user '.$name.' with an id of '.$id;
});
*/
Route::get('/', [PagesController::class, 'welcome']);
Route::get('/about', [PagesController::class, 'about']);
Route::get('/services', [PagesController::class, 'services']);

// Route::resource('posts', PostsController::class);
Route::get('posts', [PostsController::class, 'index']);
Route::get('posts/create', [PostsController::class, 'create']);
Route::get('posts/{id}', [PostsController::class, 'show']);
Route::get('posts/{id}/edit', [PostsController::class, 'edit']);
Route::post('posts', [PostsController::class, 'store']);
Route::put('posts', [PostsController::class, 'update']);
Route::delete('posts', [PostsController::class, 'destroy']);

Auth::routes();

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index']);
