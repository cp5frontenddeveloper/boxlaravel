<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SubuserController;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\CodeListController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\indexlistcontroller;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\NotificationSettingController;
use App\Http\Controllers\API\AppUpdateController;
use App\Http\Controllers\API\AdvertisementController;
use App\Http\Controllers\API\TicketTypeController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\TicketRatingController;
use App\Http\Controllers\API\TicketUpdateController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\BoxTypeController;
use App\Http\Controllers\BoxUnderManufacturingController;
use App\Http\Controllers\BoxStatusController;
use App\Http\Controllers\RepresentativeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BoxInventoryController;
use App\Http\Controllers\AdminCommunicationLogController;
use App\Http\Controllers\API\InventoryBoxController;
use App\Http\Controllers\API\ManufacturedBoxController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('cors')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::get('userscount/{id}', [UserController::class,'getusersCountByUserId']);
    Route::apiResource('subusers', SubuserController::class);
    Route::post('subuser/login', [SubuserController::class, 'login']);
    Route::get('subusers/{id}',[SubuserController::class,'show']);
    Route::delete('subusers/{id}',[SubuserController::class,'show']);
    Route::apiResource('devices', DeviceController::class);
    Route::delete('devices/{id}', [DeviceController::class, 'destroy']);
    Route::get('user/{userId}/devices', [DeviceController::class, 'show']);
    Route::put('/devices/{id}', [DeviceController::class, 'update']);
    Route::get('/device-count/{userId}', [DeviceController::class, 'getDeviceCountByUserId']);
    Route::apiResource('codelists', CodeListController::class);
    Route::post('generate', [GroupController::class, 'generateRandomNumbers']);
    Route::post('/groups/device', [GroupController::class, 'getByDeviceNumber']);
    Route::post('/groups/delete', [GroupController::class, 'deleteByDeviceNumber']);
    Route::post('/groups/deleteuserid', [GroupController::class, 'deleteByDeviceNumberUserId']);
    Route::post('/subuser/deletedsubuser', [SubuserController::class, 'deleteByDeviceNumberdeviceId']);
    Route::post('login', [LoginController::class, 'login']);
    Route::post('/verify-temp-code', [UserController::class, 'verifyTempCode']);
    Route::post('password-reset/send-code', [UserController::class, 'sendResetCode']);
    Route::post('password-reset/verify-code', [UserController::class, 'verifyResetCode']);
    Route::put('passwordupdatd', [UserController::class, 'storeNewPassword']);
    //   event api
    // Event API
    Route::post('/events', [EventController::class, 'store']);
    Route::post('/events/{id}', [EventController::class, 'show']);
    Route::post('/update-token', [TokenController::class, 'updateOrCreateToken']);
    Route::get('/update-token/{user_id}', [TokenController::class, 'getTokenByUserId']);
    Route::post('/updateorcreateindex', [indexlistcontroller::class, 'updateorcreateindex']);
    Route::get('/updateorcreateindex/{device_id}', [indexlistcontroller::class, 'getindex']);
    Route::post('/send-phone-otp', [UserController::class, 'sendOTP']);
    Route::post('/verify-phone-otp', [UserController::class, 'verifyPhoneOTP']);
    Route::get('/notifications/{user_id}', [NotificationController::class, 'index']);
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::post('/notifications/{id}', [NotificationController::class, 'update']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::get('/notification-settings/{user_id}', [NotificationSettingController::class, 'show']);
    Route::put('/notification-settings/{user_id}', [NotificationSettingController::class, 'update']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/delete-account', [UserController::class, 'deleteAccount']);
    //==================================================================
    Route::prefix('app-updates')->group(function () {
        Route::get('/', [AppUpdateController::class, 'index']);
        Route::post('/', [AppUpdateController::class, 'store']);
        Route::get('/latest', [AppUpdateController::class, 'getLatestVersion']);
        Route::get('/{id}', [AppUpdateController::class, 'show']);
        Route::put('/{id}', [AppUpdateController::class, 'update']);
        Route::delete('/{id}', [AppUpdateController::class, 'destroy']);
    });
    Route::prefix('advertisements')->group(function () {
        Route::get('/', [AdvertisementController::class, 'index']);
        Route::post('/', [AdvertisementController::class, 'store']);
        Route::get('/{id}', [AdvertisementController::class, 'show']);
        Route::put('/{id}', [AdvertisementController::class, 'update']);
        Route::delete('/{id}', [AdvertisementController::class, 'destroy']);
    });
    //================
Route::prefix('ticket-types')->group(function () {
    Route::get('/', [TicketTypeController::class, 'index']);
    Route::post('/', [TicketTypeController::class, 'store']);
    Route::get('/{id}', [TicketTypeController::class, 'show']);
    Route::put('/{id}', [TicketTypeController::class, 'update']);
    Route::delete('/{id}', [TicketTypeController::class, 'destroy']);
});
 Route::prefix('tickets')->group(function () {
    // عرض جميع التذاكر
    Route::get('/', [TicketController::class, 'index']);
    
    // عرض تذاكر مستخدم معين
    Route::get('/user/{user_id}', [TicketController::class, 'getUserTickets']);
    
    // عرض تذكرة محددة  
    Route::get('/{id}', [TicketController::class, 'show']);
    
    // إنشاء تذكرة جديدة
    Route::post('/', [TicketController::class, 'store']);
    
    // تحديث تذكرة
    Route::put('/{id}', [TicketController::class, 'update']);
    
    // حذف تذكرة
    Route::delete('/{id}', [TicketController::class, 'destroy']);
    
    // Get ticket updates
    Route::get('/{id}/updates', [TicketController::class, 'getUpdates']);
    // Add new update to ticket
    Route::post('/{id}/updates', [TicketController::class, 'addUpdate']);
    // راوتات التقييم
    Route::post('/{ticket_id}/rating', [TicketRatingController::class, 'store']);
    Route::get('/{ticket_id}/rating', [TicketRatingController::class, 'show']);
    Route::put('/{ticket_id}/rating', [TicketRatingController::class, 'update']);
});
//===============================================
Route::apiResource('workshops', WorkshopController::class);
Route::apiResource('box-types', BoxTypeController::class);
Route::apiResource('boxes-under-manufacturing', BoxUnderManufacturingController::class);
Route::apiResource('box-statuses', BoxStatusController::class);
Route::prefix('representatives')->group(function () {
    Route::post('login', [RepresentativeController::class, 'login']);
    Route::post('reset-password', [RepresentativeController::class, 'resetPassword']);
    // Admin communication logs routes
    Route::prefix('logs')->group(function () {
        Route::get('/', [AdminCommunicationLogController::class, 'index']);
        Route::post('/', [AdminCommunicationLogController::class, 'store']);
        Route::get('/{adminCommunicationLog}', [AdminCommunicationLogController::class, 'show']);
        Route::put('/{adminCommunicationLog}', [AdminCommunicationLogController::class, 'update']);
        Route::delete('/{adminCommunicationLog}', [AdminCommunicationLogController::class, 'destroy']);
    });
    
    // Representative-specific logs
    Route::get('{representative_id}/logs', [AdminCommunicationLogController::class, 'getRepresentativeLogs']);
    Route::post('{representative_id}/logs', [AdminCommunicationLogController::class, 'store']);
    
    // Representative resource routes
    Route::get('/', [RepresentativeController::class, 'index']);
    Route::post('/', [RepresentativeController::class, 'store']);
    Route::get('/{representative}', [RepresentativeController::class, 'show']);
    Route::put('/{representative}', [RepresentativeController::class, 'update']);
    Route::delete('/{representative}', [RepresentativeController::class, 'destroy']);
});
Route::apiResource('orders', OrderController::class);
Route::get('representatives/{representative_id}/orders', [OrderController::class, 'getRepresentativeOrders']);
Route::apiResource('clients', ClientController::class);
Route::apiResource('box-inventory', BoxInventoryController::class);

// Inventory Boxes Routes
Route::apiResource('inventory-boxes', InventoryBoxController::class);

// Manufactured Boxes Routes
Route::apiResource('manufactured-boxes', ManufacturedBoxController::class);
Route::post('representatives/reset-password', [RepresentativeController::class, 'resetPassword']);
});

