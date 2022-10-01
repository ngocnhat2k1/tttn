<?php

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

Route::group(['prefix' => "v1", "namespace" => "App\Http\Controllers\Api\V1"], function () {
    Route::group(['prefix' => "products", 'as' => "products."], function() {
        Route::get('/', [ProductController::class, "index"]);
        Route::get('/{id}', [ProductController::class, "show"]);
        Route::post('/add', [ProductController::class, "store"]);
        Route::put('/edit/{id}', [ProductController::class, "update"]);
        Route::delete('/destroy/{id}', [ProductController::class, "destroy"]);
    });
});
