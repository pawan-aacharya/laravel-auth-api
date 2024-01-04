<?php

use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//public route
Route::post('/register',[UserController::class,'register']);
Route::post('/login',[UserController::class,'login']);
Route::post('/send-reset-password-email',[PasswordResetController::class,'send_reset_password_email']);
Route::post('/reset_password/{token}',[PasswordResetController::class,'reset']);

//protected route
Route::middleware(['auth:sanctum'])->group(function(){
    Route::post('/logout',[UserController::class,'logout']);
    Route::get('/loggeduser',[UserController::class,'logged_user']);
    Route::post('/changepassword',[UserController::class,'change_password']);
});