<?php

use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// ALWAYS refresh your testing api client before running a test. \\

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Admin
Route::get("/admin/setup", [AdminAuthController::class, "setup"]);
Route::post("/admin/login", [AdminAuthController::class, "login"]);
Route::middleware("auth:sanctum")->group(function() {
    Route::get("/admin/profile", [AdminAuthController::class, "profile"]);
    Route::post("/admin/logout", [AdminAuthController::class, "logout"]);
});

Route::group(['prefix' => "v1", "namespace" => "App\Http\Controllers\Api\V1"], function () {
    Route::group(['prefix' => "products", 'as' => "products."], function() {
        Route::get('/', [ProductController::class, "index"]);
        Route::get('/{id}', [ProductController::class, "show"]);
        Route::post('/add', [ProductController::class, "store"]);
        Route::put('/edit/{id}', [ProductController::class, "update"]);
        Route::delete('/destroy/{id}', [ProductController::class, "destroy"]);
    });
});

// Customer
Route::post("/register", [UserAuthController::class, "register"]);
Route::post("/login", [UserAuthController::class, "login"]);
Route::middleware('auth:sanctum')->group(function() {
    Route::get("/user/profile", [UserAuthController::class, "profile"]);
    Route::post("/logout", [UserAuthController::class, "logout"]);
});
