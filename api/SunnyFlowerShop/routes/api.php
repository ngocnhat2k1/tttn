<?php

use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\FeedBackController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\FavoriteController;
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
        Route::get('/', [ProductController::class, "index"]); // Show all products
        Route::get('/{id}', [ProductController::class, "show"]); // Show detail of a specific product
        Route::post('/add', [ProductController::class, "store"]); // Add single product to database
        Route::post("/bulk", [ProductController::class, "bulkStore"]); // Add multiple product at once
        Route::put('/edit/{id}', [ProductController::class, "update"]); // Update detail of a specific product
        Route::delete('/destroy/category={category}&product={product}', [ProductController::class, "destroyCategory"]); // Delete a category from product
        Route::delete('/destroy/{id}', [ProductController::class, "destroy"]); // (Soft) Delete product from database
    });
});

// ***** CUSTOMER ***** \\
Route::post("/register", [UserAuthController::class, "register"]);
Route::post("/login", [UserAuthController::class, "login"]);
Route::middleware('auth:sanctum')->group(function() {
    Route::group(['prefix' => "user"], function() {
        // View profile
        Route::get("/profile", [UserAuthController::class, "profile"]); // May only be use for editing info in user profile page (Only for login user)

        // Create-Read-Update(Reduce quantity)-Delete Proudct from cart
        Route::get("/cart", [CartController::class, "index"]);
        Route::post("/cart/add", [CartController::class, "store"]); // Update quantity or add new product to cart - Apply in Products page and cart page
        Route::post("/cart/update", [CartController::class, "update"]); // Update quantity base on keyboard and only apply in cart page
        Route::get("/cart/reduce/{id}", [CartController::class, "reduce"]); // Reduce quantity of product in cart (only apply in cart page). May need to reconsider about GET Method
        Route::delete("/cart/destroy/{id}", [CartController::class, "destroy"]);

        // Create-Review-Cancel Order function
        Route::get("/order", [OrderController::class, "index"]); // Show all order from current login user
        Route::get("/order/{id}", [OrderController::class, "show"]); // Show detail of order from current login user
        Route::post("/order/placeorder", [OrderController::class, "store"]);// Placeorder
        Route::delete("/order/placeorder&cancel={id}", [OrderController::class, "destroy"]);// Placeorder
        // Cancel order

        // Check voucher expired date
        

        // Create-Review-Update-Delete (May be reconsider about soft delete instead) Feedback function
        Route::get("/feedback", [FeedBackController::class, "viewFeedBack"]); // Overview all feedback (still reconsider about this one)
        Route::get("/feedback/{id}", [FeedBackController::class, "feedbackDetail"]); // View detail feedback of a specific product from current login user
        Route::post("/feedback/create", [FeedBackController::class, "storeFeedBack"]); // Create new feedback for a specific proudct
        Route::put("/feedback/update/{id}", [FeedBackController::class, "updateFeedBack"]); // Update existed feedback of a specific product
        Route::delete("/feedback/destroy/{id}", [FeedBackController::class, "destroyFeedBack"]); // Delete existed feedback of a specific product

        // Create-Review-Update-Delete Address function
        Route::get("/address", [AddressController::class, "index"]);
        Route::get("/address/{id}", [AddressController::class, "show"]);
        Route::post("/address/create", [AddressController::class, "store"]);
        Route::put("/address/update/{id}", [AddressController::class, "update"]);
        Route::delete("address/destroy/{id}", [AddressController::class, "destroy"]);

        // Create-Review-Delete Products from Favorite
        Route::get("/favorite", [FavoriteController::class, "viewFavorite"]);
        Route::get("/favorite/{id}", [FavoriteController::class, "storeFavorite"]); // Add product using {id} to favorite. May need to reconsider about GET Method
        Route::delete("/favorite/destroy/{id}", [FavoriteController::class, "destroyFavorite"]);
    });
    Route::post("/logout", [UserAuthController::class, "logout"]);
});
