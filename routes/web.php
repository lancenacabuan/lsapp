<?php
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\StockRequestController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\AssemblyController;
use App\Http\Controllers\FileMaintenanceController;

Auth::routes(['register' => false, 'verify' => false, 'confirm' => false]);

//Index
Route::get('/',[PagesController::class,'index']);
Route::get('/index_data',[PagesController::class,'index_data']);
//

//Change Password
Route::get('/changepassword',[PagesController::class,'changepassword']);
Route::any('/password_save',[PagesController::class,'password_save']);
//

//Users
Route::get('/users',[PagesController::class,'users']);
Route::get('/users_data',[PagesController::class,'users_data']);
Route::any('/users/save',[PagesController::class,'users_save']);
Route::any('/logNewUser',[PagesController::class,'logNewUser']);
Route::any('/users/update',[PagesController::class,'users_update']);
//

//Stocks
Route::get('/stocks',[StocksController::class,'stocks']);
Route::get('/GetLocation',[StocksController::class,'GetLocation']);
Route::any('/stocks/save',[StocksController::class,'store']);
Route::any('/stocks/update',[StocksController::class,'update']);
Route::get('/category_data',[StocksController::class,'category_data']);
Route::get('/item_data',[StocksController::class,'item_data']);
Route::get('/itemserial_data',[StocksController::class,'itemserial_data']);

Route::get('/items',[StocksController::class,'items']);
Route::get('/getUOM',[StocksController::class,'getUOM']);
Route::get('/addStockitem',[StocksController::class,'addStockitem']);
Route::get('/itemstrans',[StocksController::class,'itemstrans']);
Route::get('/locations',[StocksController::class,'locations']);
Route::get('/stocksAvailable',[StocksController::class,'stocksAvailable']);
Route::get('/stockItem',[StocksController::class,'stockItem']);
Route::get('/item',[StocksController::class,'item']);
Route::get('/stock_data',[StocksController::class,'stock_data']);
//

//Stock Requests
Route::get('/stockrequest',[StockRequestController::class,'stockrequest']);
Route::get('/generatedr',[StockRequestController::class,'generatedr']);
Route::get('/itemsreq',[StockRequestController::class,'itemsreq']);
Route::get('/setuom',[StockRequestController::class,'setuom']);
Route::any('/saveReqNum',[StockRequestController::class,'saveReqNum']);
Route::any('/saveRequest',[StockRequestController::class,'saveRequest']);
Route::any('/logSave',[StockRequestController::class,'logSave']);
Route::get('/request_data',[StockRequestController::class,'request_data']);
Route::get('/reqModal',[StockRequestController::class,'reqModal']);
Route::get('/requestDetails',[StockRequestController::class,'requestDetails']);
Route::get('/schedItems',[StockRequestController::class,'schedItems']);
Route::any('/editSerial',[StockRequestController::class,'editSerial']);
Route::any('/delReqItem',[StockRequestController::class,'delReqItem']);
Route::any('/deleteRequest',[StockRequestController::class,'deleteRequest']);
Route::any('/approveRequest',[StockRequestController::class,'approveRequest']);
Route::any('/disapproveRequest',[StockRequestController::class,'disapproveRequest']);
Route::any('/logDisapprove',[StockRequestController::class,'logDisapprove']);
Route::any('/inTransit',[StockRequestController::class,'inTransit']);
Route::any('/receiveRequest',[StockRequestController::class,'receiveRequest']);
Route::any('/logReceive',[StockRequestController::class,'logReceive']);
Route::any('/saleRequest',[StockRequestController::class,'saleRequest']);
Route::any('/returnRequest',[StockRequestController::class,'returnRequest']);
Route::get('/stockreq',[StockRequestController::class,'stockreq']);
Route::get('/setserials',[StockRequestController::class,'setserials']);
Route::get('/setlocation',[StockRequestController::class,'setlocation']);
Route::any('/prepareItems',[StockRequestController::class,'prepareItems']);
Route::any('/logSched',[StockRequestController::class,'logSched']);
Route::get('/printRequest',[StockRequestController::class,'printRequest']);
//

//Stock Transfer
Route::get('/stocktransfer',[StockTransferController::class,'stocktransfer']);
Route::get('/generateReqNum',[StockTransferController::class,'generateReqNum']);
Route::get('/setcategory',[StockTransferController::class,'setcategory']);
Route::get('/setitems',[StockTransferController::class,'setitems']);
Route::get('/settransuom',[StockTransferController::class,'settransuom']);
Route::get('/qtystock',[StockTransferController::class,'qtystock']);
Route::any('/saveTransReqNum',[StockTransferController::class,'saveTransReqNum']);
Route::any('/saveTransRequest',[StockTransferController::class,'saveTransRequest']);
Route::any('/logTransSave',[StockTransferController::class,'logTransSave']);
Route::get('/transfer_data',[StockTransferController::class,'transfer_data']);
Route::get('/transModal',[StockTransferController::class,'transModal']);
Route::get('/transferDetails',[StockTransferController::class,'transferDetails']);
Route::get('/transItems',[StockTransferController::class,'transItems']);
Route::any('/delTransItem',[StockTransferController::class,'delTransItem']);
Route::any('/deleteTransfer',[StockTransferController::class,'deleteTransfer']);
Route::any('/approveTransfer',[StockTransferController::class,'approveTransfer']);
Route::any('/disapproveTransfer',[StockTransferController::class,'disapproveTransfer']);
Route::any('/logTransDisapprove',[StockTransferController::class,'logTransDisapprove']);
Route::any('/forReceiving',[StockTransferController::class,'forReceiving']);
Route::any('/receiveTransfer',[StockTransferController::class,'receiveTransfer']);
Route::any('/logTransReceive',[StockTransferController::class,'logTransReceive']);
Route::get('/stocktrans',[StockTransferController::class,'stocktrans']);
Route::get('/settransserials',[StockTransferController::class,'settransserials']);
Route::any('/transferItems',[StockTransferController::class,'transferItems']);
Route::any('/logTransSched',[StockTransferController::class,'logTransSched']);
Route::get('/printTransferRequest',[StockTransferController::class,'printTransferRequest']);
//

//Assembly
Route::get('/assembly',[AssemblyController::class,'assembly']);
Route::get('/itemsAssembly',[AssemblyController::class,'itemsAssembly']);
Route::get('/uomAssembly',[AssemblyController::class,'uomAssembly']);
Route::any('/createItem',[AssemblyController::class,'createItem']);
Route::any('/saveParts',[AssemblyController::class,'saveParts']);
Route::any('/logItem',[AssemblyController::class,'logItem']);
Route::get('/itemDetails',[AssemblyController::class,'itemDetails']);
Route::any('/changeItem',[AssemblyController::class,'changeItem']);
//

//Maintenance
Route::get('/maintenance',[FileMaintenanceController::class,'maintenance']);
Route::get('/fm_items',[FileMaintenanceController::class,'fm_items']);
Route::get('/asm_items',[FileMaintenanceController::class,'asm_items']);
Route::get('/fm_categories',[FileMaintenanceController::class,'fm_categories']);
Route::get('/fm_locations',[FileMaintenanceController::class,'fm_locations']);
Route::any('/saveItem',[FileMaintenanceController::class,'saveItem']);
Route::any('/updateItem',[FileMaintenanceController::class,'updateItem']);
Route::any('/saveCategory',[FileMaintenanceController::class,'saveCategory']);
Route::any('/logNewCategory',[FileMaintenanceController::class,'logNewCategory']);
Route::any('/updateCategory',[FileMaintenanceController::class,'updateCategory']);
Route::any('/logUpdateCategory',[FileMaintenanceController::class,'logUpdateCategory']);
Route::any('/saveLocation',[FileMaintenanceController::class,'saveLocation']);
Route::any('/logNewLocation',[FileMaintenanceController::class,'logNewLocation']);
Route::any('/updateLocation',[FileMaintenanceController::class,'updateLocation']);
Route::any('/requestStatusChange',[FileMaintenanceController::class,'requestStatusChange']);
//