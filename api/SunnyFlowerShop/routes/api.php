<?php

use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\OrderController;
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
// Route::get("/admin/setup", [AdminAuthController::class, "setup"]);
Route::post("/admin/login", [AdminAuthController::class, "login"]);
Route::middleware("auth:sanctum")->group(function() {
    Route::get("/admin/profile", [AdminAuthController::class, "profile"]);
    Route::post("/admin/logout", [AdminAuthController::class, "logout"]);
});

Route::group(['prefix' => "v1", "namespace" => "App\Http\Controllers\Api\V1"], function () {
    Route::group(['prefix' => "products"], function() {
        Route::get('/', [ProductController::class, "index"]);
        Route::get('/{id}', [ProductController::class, "show"]);
        Route::post('/add', [ProductController::class, "store"]);
        Route::put('/edit/{id}', [ProductController::class, "update"]);
        Route::delete('/destroy/category={category}&product={product}', [ProductController::class, "destroyCategory"]);
        Route::delete('/destroy/{id}', [ProductController::class, "destroy"]);
    });
});

// ***** CUSTOMER ***** \\
Route::post("/register", [UserAuthController::class, "register"]);
Route::post("/login", [UserAuthController::class, "login"]);
Route::middleware('auth:sanctum')->group(function() {
    Route::group(['prefix' => "user"], function() {
        // View profile
        Route::get("/profile", [UserAuthController::class, "profile"]);

        // CRD Order function
        Route::get("/order", [OrderController::class, "index"]);
        Route::get("/order/{order}", [OrderController::class, "show"]);

        // CRUD Feedback function
        Route::get("/feedback", [CustomerController::class, "viewFeedBack"]);
        Route::get("/feedback/{id}", [CustomerController::class, "feedbackDetail"]);
        Route::post("/feedback/create", [CustomerController::class, "storeFeedBack"]);
        Route::put("/feedback/update/{id}", [CustomerController::class, "updateFeedBack"]);
        Route::delete("/feedback/destroy/{id}", [CustomerController::class, "destroyFeedBack"]);

        // CRUD Address function
        Route::get("/address", [AddressController::class, "index"]);
        Route::get("/address/{id}", [AddressController::class, "show"]);
        Route::post("/address/create", [AddressController::class, "store"]);
        Route::put("/address/update/{id}", [AddressController::class, "update"]);
        Route::delete("address/destroy/{id}", [AddressController::class, "destroy"]);


        // Route::post("user/favorite/{id}", [CustomerController::class, "storeFavourite"]);
        // Route::post("/user/order/placeorder", [OrderController::class, "store"]);
    });
    Route::post("/logout", [UserAuthController::class, "logout"]);
});
