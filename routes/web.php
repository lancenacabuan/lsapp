<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\StockRequestController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\Auth\LoginController;
use Spatie\Permission\Middleware\RoleMiddleware;

Auth::routes();

//Index
Route::get('/', [PagesController::class, 'index']);
Route::get('/index_data', [PagesController::class, 'index_data']);
//

// Route::get('/joborder', [PagesController::class, 'joborder']);
Route::get('/assembly', [PagesController::class, 'assembly']);
// Route::get('/pullout', [PagesController::class, 'pullout']);
Route::get('/filemaintenance', [PagesController::class, 'filemaintenance']);

//Change Password
Route::get('/changepassword', [PagesController::class, 'changepassword']);
Route::any('/password_save',[PagesController::class,'password_save']);
//

//Users
Route::get('/users', [PagesController::class, 'users']);
Route::get('/users_data', [PagesController::class, 'users_data']);
Route::any('/users/save',[PagesController::class,'users_store']);
Route::any('/users/update',[PagesController::class,'users_update']);
//

//Stocks
Route::get('/stocks', [StocksController::class, 'stocks']);
Route::any('/stocks/save',[StocksController::class,'store']);
Route::any('/stocks/update',[StocksController::class,'update']);
Route::get('/category_data', [StocksController::class, 'category_data']);
Route::get('/item_data', [StocksController::class, 'item_data']);

Route::get('/items',[StocksController::class,'items']);
Route::get('/addStockitem',[StocksController::class,'addStockitem']);
Route::get('/itemstrans',[StocksController::class,'itemstrans']);
Route::get('/locations',[StocksController::class,'locations']);
Route::get('/stocksAvailable',[StocksController::class,'stocksAvailable']);
Route::get('/stockItem',[StocksController::class,'stockItem']);
Route::get('item',[StocksController::class,'item']);
//

//Stock Requests
Route::get('/stockrequest', [StockRequestController::class, 'stockrequest']);
Route::get('/stockreq', [StockRequestController::class, 'stockreq']);
Route::get('/request_data', [StockRequestController::class, 'request_data']);
Route::get('/schedItems', [StockRequestController::class, 'schedItems']);
Route::get('/itemsreq', [StockRequestController::class, 'itemsreq']);
// Route::get('/itemsqty', [StockRequestController::class, 'itemsqty']);
// Route::any('/itemsstock', [StockRequestController::class, 'itemsstock']);
Route::get('/generatedr', [StockRequestController::class, 'generatedr']);
Route::any('/prepareItems', [StockRequestController::class, 'prepareItems']);
Route::any('/logSched', [StockRequestController::class, 'logSched']);
Route::any('/approveRequest', [StockRequestController::class, 'approveRequest']);
Route::any('/disapproveRequest', [StockRequestController::class, 'disapproveRequest']);
Route::any('/receiveRequest', [StockRequestController::class,'receiveRequest']);
Route::any('/saveRequest', [StockRequestController::class, 'saveRequest']);
Route::post('/saveReqNum', [StockRequestController::class, 'saveReqNum']);
Route::get('/requestDetails', [StockRequestController::class, 'requestDetails']);
Route::get('/deleteRequest', [StockRequestController::class, 'deleteRequest']);
Route::any('/delReqItem', [StockRequestController::class, 'delReqItem']);
Route::any('/editSerial', [StockRequestController::class, 'editSerial']);
Route::any('/inTransit', [StockRequestController::class, 'inTransit']);
Route::any('/printRequest', [StockRequestController::class, 'printRequest']);
//

//Email
Route::get('/send-mail', function () {
    $details = ['title' => 'IDSI Main Warehouse Stock Monitoring System'];
    \Mail::to($_SESSION['email'])->send(new \App\Mail\emailNewUser($details));
});
//

