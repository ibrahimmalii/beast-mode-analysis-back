<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\KeyStatisticsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StockPropertieController;
use App\Http\Controllers\SymbolController;
use Illuminate\Support\Facades\Http;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login' ,[AuthController::class , 'login'] );
Route::get('/logout' , [AuthController::class, 'logout']);

// Reset password
Route::post('/submit-new-password/{token}', [ AuthController::class ,'resetPassword']);
Route::post('/reset_password_without_token' , [AuthController::class, 'validatePasswordRequest']);
Route::get('/reset-password/{token}', [AuthController::class, 'getResetPassword'])->name('getResetPassword');




$responseData = '';
Route::get('/test', function(Request $request){
    $response = Http::get('https://public-api.quickfs.net/v1/data/all-data/FB:US?api_key=4ed0f30c148834139f4bb3c4421341690f3d3c07');
    $response->json();
    $responseData = $response->json();
    dd($responseData);
});


// Crud For Key Statistics
// Route::post('/keyStatistics', [KeyStatisticsController::class , 'create'])->middleware('auth:sanctum');
Route::post('/keyStatistics', [KeyStatisticsController::class , 'create'])->middleware('auth:sanctum');
// Route::post('/keyStatistics', [KeyStatisticsController::class , 'create']);
Route::post('/keyStatistics/update/{symbol}', [KeyStatisticsController::class , 'update'])->middleware(['auth:sanctum' , 'admin']);
Route::post('/keyStatistics/delete/{symbol}', [KeyStatisticsController::class , 'delete'])->middleware(['auth:sanctum' , 'admin']);
Route::get('/keyStatistics', [KeyStatisticsController::class , 'index'])->middleware('auth:sanctum');
Route::get('/keyStatistics/all', [KeyStatisticsController::class , 'getAllNames'])->middleware('auth:sanctum');
Route::get('/keyStatistics/{key}', [KeyStatisticsController::class , 'show'])->middleware('auth:sanctum');



// Crud For Properties
Route::get('/properties', [StockPropertieController::class , 'index'])->middleware('auth:sanctum');
// Route::post('/properties', [StockPropertieController::class , 'create'])->middleware(['auth:sanctum' , 'admin']);
Route::post('/properties', [StockPropertieController::class , 'create']);
Route::post('/properties/{prop}', [StockPropertieController::class , 'update'])->middleware(['auth:sanctum' , 'admin']);


//Crud For Symbols
// Route::get('/symbols', [SymbolController::class, 'index'])->middleware('auth:sanctum');
Route::get('/symbols', [SymbolController::class, 'index']);
// Route::post('/symbols', [SymbolController::class, 'create'])->middleware('auth:sanctum');
Route::post('/symbols', [SymbolController::class, 'create']);


//Crud For Number Of Requests
Route::get('/num-of-requests', [RequestsController::class, 'index']);
Route::post('/num-of-requests', [RequestsController::class, 'create']);
Route::post('/num-of-requests/{id}', [RequestsController::class, 'update']);


// Search
Route::get('/search/{key}', [SearchController::class, 'search']);



