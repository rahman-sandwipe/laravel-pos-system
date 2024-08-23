<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'HomePage');
});

Route::middleware('tokenVerify')->group(function () {

    // UserController
    Route::controller(UserController::class)->group(callback: function(){
        // Web API Routes
        Route::POST('/reset-password',      'resetPassword');
        Route::post('/user-update',         'UpdateProfile');

        // Page Routes
        Route::get('/user-profile',         'UserProfile');
        Route::get('/resetPassword',        'ResetPasswordPage');
        Route::get('/userProfile',          'ProfilePage');
        // User Logout
        Route::get('/logout',               'UserLogout');
    });


    // Dashboard Controller
    Route::controller(DashboardController::class)->group(callback: function(){
        Route::get('/dashboard',            'DashboardPage');
        // SUMMARY & Report
        Route::get("/summary",              'Summary');
    });


    // CustomerController
    Route::controller(CustomerController::class)->group(callback: function(){
        // Customer API
        Route::post("/create-customer",     'CustomerCreate');
        Route::get("/list-customer",        'CustomerList');
        Route::post("/delete-customer",     'CustomerDelete');
        Route::post("/update-customer",     'CustomerUpdate');
        Route::post("/customer-by-id",      'CustomerByID');

        //Customer Page
        Route::get('/customerPage',         'CustomerPage');
    });


    // CategoryController
    Route::controller(CategoryController::class)->group(callback: function(){
        // Category API
        Route::post("/create-category",     'CategoryCreate');
        Route::post("/delete-category",     'CategoryDelete');
        Route::post("/update-category",     'CategoryUpdate');
        Route::get("/list-category",        'CategoryList');
        Route::post("/category-by-id",      'CategoryByID');

        // Page Routes
        Route::get('/categoryPage',         'CategoryPage');
    });


    // ProductController
    Route::controller(ProductController::class)->group(callback: function(){
        // Product API
        Route::post("/create-product",      'CreateProduct');
        Route::post("/delete-product",      'DeleteProduct');
        Route::post("/update-product",      'UpdateProduct');
        Route::get("/list-product",         'ProductList');
        Route::post("/product-by-id",       'ProductByID');

        // Page Routes
        Route::get('/productPage',          'ProductPage');
    });


    // InvoiceController
    Route::controller(InvoiceController::class)->group(callback: function(){

        // Invoice
        Route::post("/invoice-create",      'invoiceCreate');
        Route::get("/invoice-select",       'invoiceSelect');
        Route::post("/invoice-details",     'InvoiceDetails');
        Route::post("/invoice-delete",      'invoiceDelete');

        // Page Routes
        Route::get('/invoicePage',          'InvoicePage');
        Route::get('/salePage',             'SalePage');
    });

    // Page Routes
    // ReportController
    Route::controller(ReportController::class)->group(callback: function(){
        Route::get("/sales-report/{FormDate}/{ToDate}",'SalesReport');

        Route::get('/reportPage',           'ReportPage');
    });
});

// UserController Unauthorized Route
Route::controller(UserController::class)->group(callback: function(){
    // Web API Routes
    Route::POST('/user-registration',    'userRegistration');
    Route::POST('/user-login',           'userLogin');
    Route::POST('/send-otp',             'SendOTPCode');
    Route::POST('/verify-otp',           'verifyOTP');

    // Page Routes
    Route::get('/userLogin',            'LoginPage');
    Route::get('/userRegistration',     'RegistrationPage');
    Route::get('/sendOtp',              'SendOtpPage');
    Route::get('/verifyOtp',            'VerifyOTPPage');
});

