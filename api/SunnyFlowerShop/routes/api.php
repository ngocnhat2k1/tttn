<?php

use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AddressCustomerController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CartAdminController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\FeedBackController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\FavoriteProductCustomerController;
use App\Http\Controllers\Api\V1\FeedBackAdminController;
use App\Http\Controllers\Api\V1\OrderAdminController;
use App\Http\Controllers\Api\V1\VoucherController;
use App\Http\Controllers\Api\V1\VoucherCustomerController;
use App\Http\Controllers\Api\V1\ProductQueryController;
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

// ***** Admin ***** \\
Route::get("/admin/setup", [AdminAuthController::class, "setup"]);
Route::post("/admin/login", [AdminAuthController::class, "login"]);
Route::get("/admin/retrieveToken", [AdminAuthController::class, "retrieveToken"]);
Route::middleware("auth:sanctum")->group(function () {

    // Route for admin
    Route::group(["prefix" => "admin"], function() {
        Route::get("/dashboard", [AdminAuthController::class, "dashboard"]);
        Route::get("/profile", [AdminAuthController::class, "profile"]);
        Route::put("/update", [AdminAuthController::class, "update"]);
        Route::put("/avatar/upload", [AdminAuthController::class, "upload"]);
        Route::delete("/avatar/destroy", [AdminAuthController::class, "destroyAvatar"]);
        Route::post("/logout", [AdminAuthController::class, "logout"]);
    });

    Route::group(['prefix' => "v1", "namespace" => "App\Http\Controllers\Api\V1"], function () {

        // Route for Product
        Route::group(['prefix' => "products"], function () {
            Route::get('/', [ProductController::class, "index"]); // Show all products
            Route::get('/{id}', [ProductController::class, "show"]); // Show detail of a specific product
            Route::post('/add', [ProductController::class, "store"]); // Add single product to database
            Route::post("/bulk", [ProductController::class, "bulkStore"]); // Add multiple product at once
            Route::put('/{id}/edit', [ProductController::class, "update"]); // Update detail of a specific product
            Route::delete('/destroy/category={category}&product={product}', [ProductController::class, "destroyCategory"]); // Delete a category from product
            Route::delete('/{id}/destroy={state}', [ProductController::class, "destroy"]); // (Soft) Delete product from database
            /** Need to add fucntion
             * Adjust products value function for each products
             * Favourite - Still figure this one out
             * Query products got the most favorite product out of all products
             */
        });

        // Route for manage Admin
        /**
         * View all admin
         * Create new (low level) admin
         * Update (low level) admin information
         * Delete (or Soft ?) (low level) admin account
         */
    
        // Route for User
        Route::group(['prefix' => "users"], function () {
            // User info
            Route::get("/", [CustomerController::class, "index"]); // Show all user available
            Route::get("/{customer}", [CustomerController::class, "show"]); // Show detail information from specific customer
            Route::post("/create", [CustomerController::class, "store"]); // Create account from admin site
            Route::put("{customer}/update_og", [CustomerController::class, "update"]); // Update information for specific customer from admin site
            Route::put("{customer}/update", [CustomerController::class, "updateValue"]); // Update information for specific customer from admin site
            Route::delete("/{customer}/disable={state}", [CustomerController::class, "disable"]); // Disable customer account
            Route::put("{customer}/avatar/upload", [CustomerController::class, "upload"]);
            Route::delete("{customer}/avatar/destroy", [CustomerController::class, "destroyAvatar"]);

            // Address from User info
            Route::get("/{customer}/addresses", [AddressCustomerController::class, "index"]);
            Route::get("/{customer}/address/{address}", [AddressCustomerController::class, "show"]);
            Route::post("/{customer}/address/create", [AddressCustomerController::class, "store"]);
            Route::put("/{customer}/address/{address}/update", [AddressCustomerController::class, "update"]);
            Route::delete("/{customer}/address/{address}/destroy", [AddressCustomerController::class, "destroy"]);

            // Order from User info
            Route::get("/{customer}/orders", [OrderCustomerController::class, "index"]);
            Route::get("/{customer}/orders/{order}", [OrderCustomerController::class, "show"]);
            Route::put("/{customer}/orders/{order}/update", [OrderCustomerController::class, "update"]);
            Route::put("/{customer}/orders/{order}/update/status={state}", [OrderCustomerController::class, "updateStatus"]); // Update only status of order
            Route::delete("/{customer}/orders/{order}/destroy={state}", [OrderCustomerController::class, "destroy"]);

            // Voucher from User info
            Route::get("/{customer}/vouchers", [VoucherCustomerController::class, "index"]);
            Route::get("/{customer}/voucher/{voucher}", [VoucherCustomerController::class, "show"]);
            /* Delete voucher from Customer's Order (?) */

            // Favorite from User info
            Route::get("/{customer}/favorite", [FavoriteProductCustomerController::class, "index"]);
            Route::delete("/{customer}/favorite/{product}/destroy", [FavoriteProductCustomerController::class, "destroy"]);
        });

        /** Order
         * Overview all orders order by recently_created
         * Create-Update-(Soft)Delete function for each orders from admin site
         * Update state for orders
         */
        Route::group(['prefix' => 'orders'], function() {
            Route::get("/", [OrderAdminController::class, "all"]);
        });
    
        /** Address
         * Overview addresses
         * Detail address from which customer
         * Create-Update-Delete Address from admin site
         */
        Route::group(['prefix' => 'addresses'], function() {
            Route::get("/", [AddressCustomerController::class, "all"]);
        });
    
        /** Category
         * Overview category
         * Detail category and its appearance in which products
         * Create-Update-(Soft)Delete fucntion for each categories from admin site
         */
        Route::group(['prefix' => 'categories'], function() {
            Route::get("/", [CategoryController::class, "index"]);
            Route::get("/{id}", [CategoryController::class, "show"]);
            Route::post("/create", [CategoryController::class, "store"]);
            Route::put("/{id}/update", [CategoryController::class, "update"]);
            Route::delete("/{id}/destroy", [CategoryController::class, "destroy"]);
        });
    
        /** Voucher
         * Overview all vouchers has been created so far
         * Filter for vouhcer_expired_date (Maybe for front-end side)
         * Detail for its vouhcer and its appearance in which orders
         * Create-Update-(Soft)Delete function for each vouchers
         */
        Route::group(['prefix' => 'vouchers'], function() {
            Route::get("/", [VoucherController::class, "index"]);
            Route::get("/{id}", [VoucherController::class, "show"]);
            Route::post("/create", [VoucherController::class, "store"]);
            Route::put("/{id}/update", [VoucherController::class, "update"]);
            Route::delete("/{id}/destroy={state}", [VoucherController::class, "destroy"]);
        });

        /** Feedback
         * Overview feedback
         * Create-Update-Delete feedback from admin site (?)
         */
        Route::group(['prefix' => 'feedbacks'], function() {
            Route::get("/", [FeedBackAdminController::class, "all"]);
        });

        /** Cart
         * Overview Cart from all customers
         * Detail cart from a specific customers
         * Update-Delete Cart (?)
         */
        Route::group(['prefix' => 'carts'], function() {
            Route::get("/", [CartAdminController::class, "all"]);
            // Check state before show (to determine whether it was viewed from admin POV or Customer POV)
            Route::get("/{id}", [CartAdminController::class, "index"]);
            Route::delete("/{id}/remove/{productId}", [CartAdminController::class, "removedProduct"]);
            Route::delete("/{id}/empty", [CartAdminController::class, "emptyCart"]);
            Route::put("/{id}/update", [CartAdminController::class, "update"]);
        });     
    
        /** Email
         * Manage Send Email function
         * Manage email template
         * Create forgot password function
         */
    
        /** Query for products appearance in front page
         * Trending product
         * New products
         * Best Seller
         * Sale products
         * All of them are top 10
         */
    });
});


// ***** CUSTOMER ***** \\
Route::get('/products', [ProductController::class, "index"]); // Show all products
Route::get('/products/newArrival', [ProductQueryController::class, "arrival"]); // Show all products
Route::get('/products/saleProduct', [ProductQueryController::class, "sale"]); // Show all products
Route::get('/products/bestSeller', [ProductQueryController::class, "best"]); // Show all products
Route::get('/products/{id}', [ProductController::class, "show"]); // Show detail of a specific product

Route::post("/register", [UserAuthController::class, "register"]); // Register
Route::post("/login", [UserAuthController::class, "login"]); // Login
Route::get("/retrieveToken", [UserAuthController::class, "retrieveToken"]);

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => "user"], function () {
        // View profile
        Route::get("/profile", [UserAuthController::class, "profile"]); // May only be use for editing info in user profile page (Only for login user)
        // Route::put("/update_og", [UserAuthController::class, "update"]); // Update user information
        Route::put("/update", [UserAuthController::class, "updateValue"]); // Update user information (no restrict)
        Route::put("/avatar/upload", [UserAuthController::class, "upload"]);
        Route::delete("/avatar/destroy", [UserAuthController::class, "destroyAvatar"]);

        // Create-Read-Update(Reduce quantity)-Delete Proudct from cart
        Route::get("/cart", [CartController::class, "index"]);
        Route::post("/cart/add", [CartController::class, "store"]); // Update quantity or add new product to cart - Apply in Products page and cart page
        Route::put("/cart/update", [CartController::class, "update"]); // Update quantity base on keyboard and only apply in cart page
        Route::get("/cart/reduce/{id}", [CartController::class, "reduce"]); // {id} is product_id; Reduce quantity of product in cart (only apply in cart page). May need to reconsider about GET Method
        Route::delete("/cart/destroy/{id}", [CartController::class, "destroy"]); // {id} is product_id

        // Create-Review-Cancel Order function
        Route::get("/order", [OrderController::class, "index"]); // Show all order from current login user
        Route::get("/order/{id}", [OrderController::class, "show"]); // {id} is order_id; Show detail of order from current login user
        Route::post("/order/placeorder", [OrderController::class, "store"]); // Placeorder
        Route::delete("/order/placeorder&cancel={id}", [OrderController::class, "destroy"]); // {id} is order_id; Cancel order
        Route::put("/order/{id}/status", [OrderController::class, "updateStatus"]); // Customer only allow to confirm "Completed" state for order

        // Create-Review-Update-Delete (May be reconsider about soft delete instead) Feedback function
        Route::get("/feedback", [FeedBackController::class, "viewFeedBack"]); // Overview all feedback (still reconsider about this one)
        Route::get("/feedback/{id}", [FeedBackController::class, "feedbackDetail"]); // {id} is feedback_id; View detail feedback of a specific product from current login user
        // Route::get("/feedback/product/{id}", [FeedBackController::class, "feedbackProductDetail"]); // {id} is feedback_id; View detail feedback of a specific product from current login user
        Route::post("/feedback/create", [FeedBackController::class, "storeFeedBack"]); // Create new feedback for a specific proudct
        Route::put("/feedback/update/{id}", [FeedBackController::class, "updateFeedBack"]); // {id} is feedback_id; Update existed feedback of a specific product
        Route::delete("/feedback/destroy/{id}", [FeedBackController::class, "destroyFeedBack"]); // {id} is feedback_id; Delete existed feedback of a specific product

        // Create-Review-Update-Delete Address function
        Route::get("/address", [AddressController::class, "index"]);
        Route::get("/address/{id}", [AddressController::class, "show"]); // {id} is address_id;
        Route::post("/address/create", [AddressController::class, "store"]);
        Route::put("/address/update/{id}", [AddressController::class, "update"]); // {id} is address_id;
        Route::delete("address/destroy/{id}", [AddressController::class, "destroy"]); // {id} is address_id;

        // Create-Review-Delete Products from Favorite
        Route::get("/favorite", [FavoriteController::class, "viewFavorite"]);
        Route::get("/favorite/{id}", [FavoriteController::class, "storeFavorite"]); // Using {id} to add product to favorite. May need to reconsider about GET Method
        Route::delete("/favorite/destroy/{id}", [FavoriteController::class, "destroyFavorite"]);
    });
    Route::post("/logout", [UserAuthController::class, "logout"]);
});
