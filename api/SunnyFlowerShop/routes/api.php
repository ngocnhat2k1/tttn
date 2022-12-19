<?php

use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\V1\AddressAdminController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AddressCustomerController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CartAdminController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\CheckoutPaypalController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\FeedBackController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\FavoriteProductCustomerController;
use App\Http\Controllers\Api\V1\FeedBackAdminController;
use App\Http\Controllers\api\v1\ForgotPasswordController;
use App\Http\Controllers\Api\V1\OrderAdminController;
use App\Http\Controllers\Api\V1\OrderCustomerController;
use App\Http\Controllers\Api\V1\VoucherController;
use App\Http\Controllers\Api\V1\VoucherCustomerController;
use App\Http\Controllers\Api\V1\ProductQueryController;
use App\Http\Controllers\Api\V1\UpdateOrderController;
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
// Route::post("/admin/retrieveToken", [AdminAuthController::class, "retrieveToken"]); // old one that has encrypt function
Route::get("/admin/retrieveToken", [AdminAuthController::class, "retrieveToken"]);
Route::middleware("auth:sanctum")->group(function () {

    // Route for admin
    Route::group(["prefix" => "admin"], function () {
        Route::get("/userInfo", [AdminAuthController::class, "userInfo"]);
        Route::get("/dashboard", [AdminAuthController::class, "dashboard"]);
        Route::get("/dashboardOop", [AdminAuthController::class, "dashboardOop"]);
        Route::get("/profile", [AdminAuthController::class, "profile"]);
        Route::put("/update", [AdminAuthController::class, "update"]);
        Route::put("/changePassword", [AdminAuthController::class, "changePassword"]);
        Route::put("/avatar/upload", [AdminAuthController::class, "upload"]);
        Route::delete("/avatar/destroy", [AdminAuthController::class, "destroyAvatar"]);
        Route::post("/logout", [AdminAuthController::class, "logout"]);
    });

    Route::group(['prefix' => "v1", "namespace" => "App\Http\Controllers\Api\V1"], function () {

        // Route for Product
        Route::group(['prefix' => "products"], function () {
            Route::get('/', [ProductController::class, "indexAdmin"]); // Show all products w/o paginating
            Route::get('/indexOld', [ProductController::class, "index"]); // Show all products
            Route::get('/{id}', [ProductController::class, "show"]); // Show detail of a specific product
            Route::post('/add', [ProductController::class, "store"]); // Add single product to database
            Route::post("/bulk", [ProductController::class, "bulkStore"]); // Add multiple product at once
            Route::put('/{id}/edit', [ProductController::class, "update"]); // Update detail of a specific product
            Route::put('/{id}/editNoRequired', [ProductController::class, "updateNoRequired"]); // Update detail of a specific product
            Route::put('/change/category={category}&product={product}', [ProductController::class, "changeCategory"]); // Delete a category from product
            Route::delete('/{id}/destroy={state}', [ProductController::class, "destroy"]); // (Soft) Delete product from database
            Route::delete('/destroyBulk={state}', [ProductController::class, "destroyBulk"]); // (Soft) Delete product from database
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
        Route::group(['prefix' => "admins"], function () {
            Route::get("/", [AdminController::class, "index"]);
            Route::post("/create", [AdminController::class, "store"]);
            Route::get("/{admin}", [AdminController::class, "show"]);
            Route::put("/{admin}/update", [AdminController::class, "update"]);
            Route::delete("/{admin}/delete", [AdminController::class, "destroy"]);
        });

        // Route for User
        Route::group(['prefix' => "users"], function () {
            // User info
            Route::get("/", [CustomerController::class, "index"]); // Show all user available
            Route::get("/{customer}", [CustomerController::class, "show"]); // Show detail information from specific customer
            Route::post("/create", [CustomerController::class, "store"]); // Create account from admin site
            Route::put("{customer}/update", [CustomerController::class, "update"]); // Update information for specific customer from admin site
            Route::put("{customer}/changePassword", [CustomerController::class, "changePassword"]); // Update information for specific customer from admin site
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
            Route::get("/{customer}/orders/{order}/notifyOrder={state}", [OrderCustomerController::class, "notifyOrder"]); // Update only status of order
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
        Route::group(['prefix' => 'orders'], function () {
            Route::get("/", [OrderAdminController::class, "index"]);
            Route::get("/{order}", [OrderAdminController::class, "show"]);
            Route::get("/idDelivery/{id}", [OrderAdminController::class, "showViaIdDelivery"]);
            // Route::put("/{order}/update/status={state}", [OrderAdminController::class, "updateStatus"]);
            Route::delete("/{order}/destroy={state}", [OrderAdminController::class, "destroy"]);
            Route::put("/{order}/status={state}", [OrderAdminController::class, "status"]); // Testing status order (Not apply to confirmed orders)

            // Update order status and Create-Update-Cancel Order in GiaoHangNhanh
            Route::put("/{order}/updateStatus={state}", [UpdateOrderController::class, "updateStatus"]); // Update status and create order in GiaoHangNhanh site            
            Route::post("/refresh", [UpdateOrderController::class, "refreshState"]); // Use for refresh API to update Order state
            
            Route::post("/{order}/preview", [UpdateOrderController::class, "preview"]); // Use for preview order created by Giao Hang Nhanh before creating an order in GiaoHangNhanh site [NOTE: CURRENTLY IT'S BROKEN]
        });

        /** Address
         * Overview addresses
         * Detail address from which customer
         * Create-Update-Delete Address from admin site
         */
        Route::group(['prefix' => 'addresses'], function () {
            Route::get("/", [AddressAdminController::class, "index"]);
            Route::get("/{address}", [AddressAdminController::class, "show"]);
            // Update Address ??
            Route::delete("/{address}/destroy", [AddressAdminController::class, "destroy"]);
        });

        /** Category
         * Overview category
         * Detail category and its appearance in which products
         * Create-Update-(Soft)Delete fucntion for each categories from admin site
         */
        Route::group(['prefix' => 'categories'], function () {
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
        Route::group(['prefix' => 'vouchers'], function () {
            Route::get("/", [VoucherController::class, "index"]);
            Route::get("/{id}", [VoucherController::class, "show"]);
            Route::post("/create", [VoucherController::class, "store"]);
            Route::put("/{id}/update", [VoucherController::class, "update"]);
            Route::put("/{id}/updateNoRequired", [VoucherController::class, "updateNoRequired"]);
            Route::put("/{id}/showVoucher={state}", [VoucherController::class, "showVoucher"]);
            Route::delete("/{id}/destroy={state}", [VoucherController::class, "destroy"]);
        });

        /** Feedback
         * Create-Update-Delete feedback from admin site (?)
         */
        Route::group(['prefix' => 'feedbacks'], function () {
            Route::get("/", [FeedBackAdminController::class, "all"]);
            Route::get("/{id}", [FeedBackAdminController::class, "show"]);
            // Delete Feedback ??
        });

        /** Cart
         * Overview Cart from all customers
         * Detail cart from a specific customers
         * Update-Delete Cart (?)
         */
        Route::group(['prefix' => 'carts'], function () {
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
    });
});


// ***** CUSTOMER ***** \\
Route::get("/categories", [ProductQueryController::class, "allCategories"]);
Route::get('/products', [ProductQueryController::class, "indexCustomer"]); // Show all products
Route::get('/products/{id}', [ProductQueryController::class, "show"]); // Show detail of a specific product
Route::get('/products/categories/{filter}', [ProductQueryController::class, "filterProducts"]);// Show detail of a specific product
Route::get('/products/filter/search={value}', [ProductQueryController::class, "searchProduct"])->name("filter.search"); // Show detail of a specific product
Route::get('/products/topBar/search={value}', [ProductQueryController::class, "searchTopBar"]); // Show detail of a specific product
Route::get("/feedback/product/{id}", [ProductQueryController::class, "feedbacksProduct"]);
Route::get("/show/voucher", [ProductQueryController::class, "showVoucher"]);
/** Query for products appearance in front page
 * Trending product
 * New products
 * Best Seller
 * Sale products
 * All of them are top 10
 */
Route::get('/product/newArrival', [ProductQueryController::class, "arrival"]); // Show all products
Route::get('/product/saleProduct', [ProductQueryController::class, "sale"]); // Show all products
Route::get('/product/bestSeller', [ProductQueryController::class, "best"]); // Show all products
Route::get('/product/trending/day={day}', [ProductQueryController::class, "trending"]); // Show all products
Route::get('/mostfavoriteProducts', [ProductQueryController::class, "mostfavoriteProducts"]); // Show detail of a specific product

Route::post("/register", [UserAuthController::class, "register"]); // Register
Route::post("/login", [UserAuthController::class, "login"]); // Login
Route::post("/forgotPassword", [ForgotPasswordController::class, "forgot"]);
Route::post("/checkCode", [ForgotPasswordController::class, "checkCode"]);
Route::post("/resetPassword", [ForgotPasswordController::class, "reset"]);
// Route::post("/retrieveToken", [UserAuthController::class, "retrieveToken"]); // Decrypt token to authenticate function
Route::get("/retrieveToken", [UserAuthController::class, "retrieveToken"]); // Decrypt token to authenticate function

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => "user"], function () {
    
        // View profile
        Route::get("/dashboard", [UserAuthController::class, "dashbooard"]);
        Route::get("/userInfo", [UserAuthController::class, "userInfo"]);
        Route::get("/profile", [UserAuthController::class, "profile"]); // May only be use for editing info in user profile page (Only for login user)
        Route::put("/update", [UserAuthController::class, "update"]); // Update user information
        Route::put("/changePassword", [UserAuthController::class, "changePassword"]); // Update user information (no restrict)
        Route::put("/avatar/upload", [UserAuthController::class, "upload"]);
        Route::delete("/avatar/destroy", [UserAuthController::class, "destroyAvatar"]);
        Route::get("/vipCustomer", [UserAuthController::class, "vipCustomerCheck"]); // Use for after placing order to check how many order has customer ordered to create special Voucher for only that customer

        // Create-Read-Update(Reduce quantity)-Delete Proudct from cart
        Route::get("/cart/state={state}", [CartController::class, "index"]);
        // Route::post("/cart/add", [CartController::class, "store"]); // Update quantity or add new product to cart - Apply in Products page and cart page
        Route::post("/cart/add/{id}", [CartController::class, "singleQuantity"]); // Update quantity or add new product to cart - Apply in Products page and cart page
        Route::put("/cart/update", [CartController::class, "update"]); // Update quantity base on keyboard and only apply in cart page
        Route::post("/cart/reduce/{id}", [CartController::class, "reduce"]); // {id} is product_id; Reduce quantity of product in cart (only apply in cart page). May need to reconsider about GET Method
        Route::delete("/cart/destroy/{id}", [CartController::class, "destroy"]); // {id} is product_id
        Route::delete("/cart/empty", [CartController::class, "empty"]); // {id} is product_id

        /**  ORDER FUNCTION */
        // Create-Review-Cancel Order function
        Route::get("/order", [OrderController::class, "index"]); // Show all order from current login user
        Route::get("/order/{id}", [OrderController::class, "show"]); // {id} is order_id; Show detail of order from current login user
        Route::get("/order/idDelivery/{id}", [OrderController::class, "showViaIdDelivery"]); // {id} is order_id; Show detail of order from current login user
        Route::post("/order/placeorder", [CheckoutController::class, "store"]); // Placeorder
        // Route::post("/order/placeorderPaypal", [CheckoutPaypalController::class, "store"]); // Placeorder
        Route::get("/order/{id}/payment", [CheckoutController::class, "redirect"])->name("redirect.page"); // Use for momo

        // Check voucher process
        Route::post("/voucherCheck", [VoucherCustomerController::class, "checkVoucher"]);

        // After payment completed
        // Route::get(
        //     "/order/payment?partnerCode={partnerCode}&orderId={orderId}&requestId={requestId}&amount={amount}&orderInfo={orderInfo}&orderType={orderType}&transId={transId}&resultCode={resultCode}&message={message}&payType={payType}&responseTime={responseTime}&extraData={extraData}&signature={signature}",
        //     [CheckoutController::class, "redirect"]
        // )->name("return.page");
        Route::post("/order/complete/payment", [CheckoutController::class, "redirect"]); // Use this when front-end can't get header redirect URL

        Route::delete("/order/{idDelivery}/cancel", [OrderController::class, "destroy"]); // {id} is order_id; Cancel order
        Route::put("/order/{id}/status", [OrderController::class, "updateStatus"]); // Customer only allow to confirm "Completed" state for order
        /** END OF ORDER FUNCTION */


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
        // Should use POST or GET for Adding product to Favorite (??)
        Route::post("/favorite/{id}", [FavoriteController::class, "storeFavorite"]); // Using {id} to add product to favorite. May need to reconsider about GET Method
        Route::delete("/favorite/destroy/{id}", [FavoriteController::class, "destroyFavorite"]);
    });
    Route::post("/logout", [UserAuthController::class, "logout"]);
});
