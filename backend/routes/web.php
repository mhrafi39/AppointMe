<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ServicesController;

use App\Http\Controllers\ServicePageController;

use App\Http\Controllers\ReviewController;

use App\Http\Controllers\PaymentController;

use App\Http\Controllers\SearchController;

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\ContactController;



Route::get('/', function () {
    return view('welcome');
});

// Route::view('home','home');

//Route::view('card','card');



Route::view('services-form','servicesForm');

// all ServiceController routes

Route::post('addService', [ServicesController::class, 'addService']);

Route::get('card-data/{user_id}', [ServicesController::class, 'cardData'])->name('cardData');

Route::get('home', [ServicesController::class, 'home_card'])->name('home');


//show review successful completed message
Route::get('services/{services_id}', [ServicesController::class, 'show'])->name('services.show');

//show review successful message
Route::get('/service/{services_id}', [ServicesController::class, 'show'])->name('service.show');

//show the payment successful message
Route::get('/service/{services_id}', [ServicesController::class, 'show'])->name('service.show');

//servicesPage
Route::get('servicePage/{services_id}', [ServicesController::class, 'show'])->name('servicePage');





//bookings
Route::post('addBookings', [ServicePageController::class, 'addBookings']);



//reviews
Route::post('addReview', [ReviewController::class, 'addReview']);




///payment
Route::view('payment','payment');
Route::post('addPayment', [PaymentController::class, 'addPayment']);




//search
Route::get('search',[SearchController::class,'search']);
Route::get('search', [SearchController::class, 'search'])->name('search');




//profile
Route::view('profile','profile');
Route::post('upload',[ProfileController::class, 'upload']);
Route::get('show_profile_picture',[ProfileController::class,'show_profile_picture']);



//edit profile
Route::view('edit-profile','editProfile');

// Contact 
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');




