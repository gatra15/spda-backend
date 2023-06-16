<?php

use App\Http\Controllers\API\ApprovalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use League\CommonMark\Node\Block\Document;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RoomController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TableController;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\DocumentController;

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
Route::post('/devices/start-check-status', [DeviceController::class, 'startCheckStatus']);

Route::prefix('auth')->group(function()
{
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::group(['middleware' => ['jwt.verify']], function($router){
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::group(['middleware' => ['role:Admin']], function(){
        Route::prefix('users')->group(function(){
            Route::get('/data', [UserController::class, 'index']);
            Route::get('/update/{id}', [UserController::class, 'edit']);
            Route::post('/update/{id}', [UserController::class, 'update']);
            Route::post('/data', [UserController::class, 'create']);
            Route::post('/delete/{id}', [UserController::class, 'delete']);
            Route::post('/restore/{id}', [UserController::class, 'restore']);
            Route::post('/permanet-delete/{id}', [UserController::class, 'permanent']);
            Route::get('/roles/list', [UserController::class, 'roleList']);
        });
    });

    Route::group(['middleware' => ['role:Operator']], function()
    {
        Route::prefix('documents')->group(function(){
            Route::get('/data', [DocumentController::class, 'index']);
            Route::post('/data', [DocumentController::class, 'store']);
            Route::get('/data/{id}', [DocumentController::class, 'detail']);
            Route::post('/update/{id}', [DocumentController::class, 'update']);
            Route::post('/delete/{id}', [DocumentController::class, 'deleteCustom']);
            Route::post('/restore/{id}', [DocumentController::class, 'restore']);
            Route::post('/permanent-delete/{id}', [DocumentController::class, 'destroy']);
        });
        Route::prefix('devices')->group(function(){
            Route::get('/data', [DeviceController::class, 'index']);
            Route::post('/data', [DeviceController::class, 'store']);
            Route::get('/list', [DeviceController::class, 'list']);
            Route::get('/data/{id}', [DeviceController::class, 'detail']);
            Route::post('/update/{id}', [DeviceController::class, 'update']);
            Route::post('/delete/{id}', [DeviceController::class, 'deleteCustom']);
            Route::post('/restore/{id}', [DeviceController::class, 'restore']);
            Route::post('/permanent-delete/{id}', [DeviceController::class, 'destroy']);
        });
        Route::prefix('tags')->group(function(){
            Route::get('/data', [TagController::class, 'index']);
            Route::post('/data', [TagController::class, 'store']);
            Route::get('/list', [TagController::class, 'list']);
            Route::get('/data/{id}', [TagController::class, 'detail']);
            Route::post('/update/{id}', [TagController::class, 'update']);
            Route::post('/delete/{id}', [TagController::class, 'deleteCustom']);
            Route::post('/restore/{id}', [TagController::class, 'restore']);
            Route::post('/permanent-delete/{id}', [TagController::class, 'destroy']);
        });
        Route::prefix('tables')->group(function(){
            Route::get('/data', [TableController::class, 'index']);
            Route::post('/data', [TableController::class, 'store']);
            Route::get('/list', [TableController::class, 'list']);
            Route::get('/data/{id}', [TableController::class, 'detail']);
            Route::post('/update/{id}', [TableController::class, 'update']);
            Route::post('/delete/{id}', [TableController::class, 'deleteCustom']);
            Route::post('/restore/{id}', [TableController::class, 'restore']);
            Route::post('/permanent-delete/{id}', [TableController::class, 'destroy']);
        });
        Route::prefix('rooms')->group(function(){
            Route::get('/data', [RoomController::class, 'index']);
            Route::post('/data', [RoomController::class, 'store']);
            Route::get('/list', [RoomController::class, 'list']);
            Route::get('/data/{id}', [RoomController::class, 'detail']);
            Route::post('/update/{id}', [RoomController::class, 'update']);
            Route::post('/delete/{id}', [RoomController::class, 'deleteCustom']);
            Route::post('/restore/{id}', [RoomController::class, 'restore']);
            Route::post('/permanent-delete/{id}', [RoomController::class, 'destroy']);
        });
    });

    Route::group(['middleware' => ['role:Supervisor']], function(){
        Route::post('approvals/approve/{id}', [ApprovalController::class, 'approve']);
        Route::post('approvals/reject/{id}', [ApprovalController::class, 'reject']);
    });

    Route::group(['middleware' => ['role:Manager|Operator|Supervisor']], function(){
        Route::get('documents/data', [DocumentController::class, 'index']);
        Route::get('documents/data/{id}', [DocumentController::class, 'detail']);

        Route::get('devices/data', [DeviceController::class, 'index']);
        Route::get('devices/list', [DeviceController::class, 'list']);
        Route::get('devices/data/{id}', [DeviceController::class, 'detail']);

        Route::get('tags/data', [TagController::class, 'index']);
        Route::get('tags/list', [TagController::class, 'list']);
        Route::get('tags/data/{id}', [TagController::class, 'detail']);

        Route::get('rooms/data', [RoomController::class, 'index']);
        Route::get('rooms/list', [RoomController::class, 'list']);
        Route::get('rooms/data/{id}', [RoomController::class, 'detail']);

        Route::get('tables/data', [TableController::class, 'index']);
        Route::get('tables/list', [TableController::class, 'list']);
        Route::get('tables/data/{id}', [TableController::class, 'detail']);

        Route::get('approvals/data', [ApprovalController::class, 'index']);
        Route::get('approvals/data/{id}', [ApprovalController::class, 'detail']);
    });

    Route::group(['middleware' => ['role:Admin|Manager|Operator|Supevisor|User']], function(){
        Route::get('users/profile', [UserController::class, 'detail']);
        Route::get('documents/data', [DocumentController::class, 'index']);
        Route::get('documents/data/{id}', [DocumentController::class, 'detail']);
    });
});
