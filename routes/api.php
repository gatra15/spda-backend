<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\UserController;

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

Route::prefix('users')->group(function(){
    Route::apiResource('users', 'App\Http\Controller\API\UserController');
});

Route::post('/documents/update-document', [DocumentController::class, 'updateDocument']);

Route::prefix('auth')->group(function()
{
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::group(['middleware' => ['jwt.verify']], function($router){
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::prefix('users')->group(function(){
        Route::get('/data', [UserController::class, 'index']);
        Route::get('/profile', [UserController::class, 'detail']);
        Route::get('/update/{id}', [UserController::class, 'edit']);
        Route::post('/update/{id}', [UserController::class, 'update']);
        Route::post('/delete/{id}', [UserController::class, 'delete']);
        Route::post('/restore/{id}', [UserController::class, 'restore']);
        Route::post('/permanet-delete/{id}', [UserController::class, 'permanent']);
    });

    Route::prefix('documents')->group(function(){
        Route::get('/data', [DocumentController::class, 'index']);
        Route::post('/data', [DocumentController::class, 'store']);
        Route::get('/data/{id}', [DocumentController::class, 'detail']);
        Route::post('/update/{id}', [DocumentController::class, 'update']);
        Route::post('/delete/{id}', [DocumentController::class, 'delete']);
        Route::post('/restore/{id}', [DocumentController::class, 'restore']);
        Route::post('/permanent-delete/{id}', [DocumentController::class, 'destroy']);
    });
    Route::prefix('devices')->group(function(){
        Route::get('/data', [DeviceController::class, 'index']);
        Route::post('/data', [DeviceController::class, 'store']);
        Route::get('/list', [DeviceController::class, 'list']);
        Route::get('/data/{id}', [DeviceController::class, 'detail']);
        Route::post('/update/{id}', [DeviceController::class, 'update']);
        Route::post('/delete/{id}', [DeviceController::class, 'delete']);
        Route::post('/restore/{id}', [DeviceController::class, 'restore']);
        Route::post('/permanent-delete/{id}', [DeviceController::class, 'destroy']);
    });
    Route::prefix('tags')->group(function(){
        Route::get('/data', [TagController::class, 'index']);
        Route::post('/data', [TagController::class, 'store']);
        Route::get('/list', [TagController::class, 'list']);
        Route::get('/data/{id}', [TagController::class, 'detail']);
        Route::post('/update/{id}', [TagController::class, 'update']);
        Route::post('/delete/{id}', [TagController::class, 'delete']);
        Route::post('/restore/{id}', [TagController::class, 'restore']);
        Route::post('/permanent-delete/{id}', [TagController::class, 'destroy']);
    });
});
