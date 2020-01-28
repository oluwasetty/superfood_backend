<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

// register user
Route::post('register', 'ApiAuthController@register');
// login user
Route::post('login', 'ApiAuthController@authenticate');

// Route::get('open', 'ApiAuthController@open');

// middleware for logged in user
Route::group(['middleware' => ['jwt.verify']], function () {
    // get logged in user
    Route::get('user', 'ApiAuthController@getAuthenticatedUser');
    // Route::get('closed', 'ApiAuthController@closed');
});

// route to roles resource
Route::resource('roles', 'RoleController');
// route to categories resource
Route::resource('categories', 'CategoryController');
// route to products resource
Route::resource('products', 'ProductController');
// route to carts resource
Route::resource('carts', 'CartController');
// route to send a contact message
Route::post('contact', 'HomeController@contact');
// route to subscribe to newsletter
Route::post('subscribe', 'HomeController@subscribe');
// route to unsubscribe from newsletter
Route::post('unsubscribe', 'HomeController@unsubscribe');
// route to send newsletter
Route::post('newsletter', 'HomeController@newsletter');
// route to get all emails subscribed to newsletter
Route::get('getmails', 'HomeController@getmails');
// route to list products on the home page
Route::get('products_in_store', 'ProductController@productsInStore');
// route to list products per category
Route::get('products_in_category/{category}', 'ProductController@productsInCategory');
// route to search products by name or category
Route::get('search_products/{search_query}', 'ProductController@productsSearch');
// route to search products by name or category
Route::get('single_product/{category}/{product}', 'ProductController@viewProduct');
// route to add to cart
Route::get('clear_cart/{cart_id}', 'CartController@clearCart');
// route to add to empowerment
Route::post('empowerment', 'EmpowermentController@empower');
