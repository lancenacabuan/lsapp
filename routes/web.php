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
Route::fallback(function(){ return redirect("/"); });

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
Route::get('/getInclusive',[StockRequestController::class,'getInclusive']);
Route::get('/itemsreq',[StockRequestController::class,'itemsreq']);
Route::get('/setuom',[StockRequestController::class,'setuom']);
Route::any('/saveReqNum',[StockRequestController::class,'saveReqNum']);
Route::any('/saveRequest',[StockRequestController::class,'saveRequest']);
Route::any('/logSave',[StockRequestController::class,'logSave']);
Route::any('/uploadFile',[StockRequestController::class,'uploadFile'])->name('uploadFile');
Route::get('/request_data',[StockRequestController::class,'request_data']);
Route::get('/reqModal',[StockRequestController::class,'reqModal']);
Route::get('/requestDetails',[StockRequestController::class,'requestDetails']);
Route::get('/receivedItems',[StockRequestController::class,'receivedItems']);
Route::get('/schedItems',[StockRequestController::class,'schedItems']);
Route::get('/incItems',[StockRequestController::class,'incItems']);
Route::get('/dfcItems',[StockRequestController::class,'dfcItems']);
Route::get('/incdfcItems',[StockRequestController::class,'incdfcItems']);
Route::get('/asmItems',[StockRequestController::class,'asmItems']);
Route::any('/editSerial',[StockRequestController::class,'editSerial']);
Route::any('/delReqItem',[StockRequestController::class,'delReqItem']);
Route::any('/deleteRequest',[StockRequestController::class,'deleteRequest']);
Route::any('/approveRequest',[StockRequestController::class,'approveRequest']);
Route::any('/disapproveRequest',[StockRequestController::class,'disapproveRequest']);
Route::any('/logDisapprove',[StockRequestController::class,'logDisapprove']);
Route::any('/reschedRequest',[StockRequestController::class,'reschedRequest']);
Route::any('/inTransit',[StockRequestController::class,'inTransit']);
Route::any('/saleRequest',[StockRequestController::class,'saleRequest']);
Route::any('/logSold',[StockRequestController::class,'logSold']);
Route::any('/returnRequest',[StockRequestController::class,'returnRequest']);
Route::get('/checkStatus',[StockRequestController::class,'checkStatus']);
Route::get('/stockreq',[StockRequestController::class,'stockreq']);
Route::get('/setserials',[StockRequestController::class,'setserials']);
Route::get('/setlocation',[StockRequestController::class,'setlocation']);
Route::any('/prepareItems',[StockRequestController::class,'prepareItems']);
Route::any('/logSched',[StockRequestController::class,'logSched']);
Route::any('/receiveRequest',[StockRequestController::class,'receiveRequest']);
Route::any('/receiveItems',[StockRequestController::class,'receiveItems']);
Route::any('/logReceive',[StockRequestController::class,'logReceive']);
Route::any('/receiveDefective',[StockRequestController::class,'receiveDefective']);
Route::any('/receiveDfcItems',[StockRequestController::class,'receiveDfcItems']);
Route::any('/logReceiveDfc',[StockRequestController::class,'logReceiveDfc']);
Route::get('/checkProcessed',[StockRequestController::class,'checkProcessed']);
Route::get('/getReceive',[StockRequestController::class,'getReceive']);
Route::get('/getLink',[StockRequestController::class,'getLink']);
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
Route::get('/incTransItems',[StockTransferController::class,'incTransItems']);
Route::any('/editTransSerial',[StockTransferController::class,'editTransSerial']);
Route::any('/delTransItem',[StockTransferController::class,'delTransItem']);
Route::any('/deleteTransfer',[StockTransferController::class,'deleteTransfer']);
Route::any('/approveTransfer',[StockTransferController::class,'approveTransfer']);
Route::any('/disapproveTransfer',[StockTransferController::class,'disapproveTransfer']);
Route::any('/logTransDisapprove',[StockTransferController::class,'logTransDisapprove']);
Route::any('/reschedTransRequest',[StockTransferController::class,'reschedTransRequest']);
Route::any('/forReceiving',[StockTransferController::class,'forReceiving']);
Route::any('/receiveTransfer',[StockTransferController::class,'receiveTransfer']);
Route::any('/receiveTransItems',[StockTransferController::class,'receiveTransItems']);
Route::any('/logTransReceive',[StockTransferController::class,'logTransReceive']);
Route::get('/stocktrans',[StockTransferController::class,'stocktrans']);
Route::get('/settransserials',[StockTransferController::class,'settransserials']);
Route::any('/transferItems',[StockTransferController::class,'transferItems']);
Route::any('/logTransSched',[StockTransferController::class,'logTransSched']);
Route::get('/printTransferRequest',[StockTransferController::class,'printTransferRequest']);
//

//Assembly
Route::get('/assembly',[AssemblyController::class,'assembly']);
Route::get('/assembly/generateReqNum',[AssemblyController::class,'generateReqNum']);
Route::get('/itemsAssembly',[AssemblyController::class,'itemsAssembly']);
Route::get('/uomAssembly',[AssemblyController::class,'uomAssembly']);
Route::any('/assembly/saveReqNum',[AssemblyController::class,'saveReqNum']);
Route::any('/assembly/saveRequest',[AssemblyController::class,'saveRequest']);
Route::any('/assembly/logSave',[AssemblyController::class,'logSave']);
Route::get('/assembly/request_data',[AssemblyController::class,'request_data']);
Route::any('/assembly/receiveRequest',[AssemblyController::class,'receiveRequest']);
Route::any('/assembly/receiveItems',[AssemblyController::class,'receiveItems']);
Route::any('/assembly/logReceive',[AssemblyController::class,'logReceive']);
Route::any('/assembly/defectiveRequest',[AssemblyController::class,'defectiveRequest']);
Route::any('/assembly/defectiveItems',[AssemblyController::class,'defectiveItems']);
Route::any('/assembly/logDefective',[AssemblyController::class,'logDefective']);
Route::any('/assembly/assembleRequest',[AssemblyController::class,'assembleRequest']);
Route::any('/assembly/receiveAssembled',[AssemblyController::class,'receiveAssembled']);
Route::any('/assembly/addAssembled',[AssemblyController::class,'addAssembled']);
Route::any('/assembly/logAssembled',[AssemblyController::class,'logAssembled']);
Route::any('/createItem',[AssemblyController::class,'createItem']);
Route::any('/saveParts',[AssemblyController::class,'saveParts']);
Route::any('/logItem',[AssemblyController::class,'logItem']);
Route::get('/itemDetails',[AssemblyController::class,'itemDetails']);
Route::any('/changeItem',[AssemblyController::class,'changeItem']);
Route::get('/partsDetails',[AssemblyController::class,'partsDetails']);
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
Route::get('/GetWarranty',[FileMaintenanceController::class,'GetWarranty'])->name('GetWarranty');
Route::any('/AddWarranty',[FileMaintenanceController::class,'AddWarranty']);
Route::any('/UpdateWarranty',[FileMaintenanceController::class,'UpdateWarranty']);
//