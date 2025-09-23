<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    ServicesController,
    ServicePageController,
    ReviewController,
    PaymentController,
    SearchController,
    ProfileController,
    UserAuthController,
    AdminAuthController,
    AdminProfileController,
    ContactController,
    ProviderApplicationController,
    NotificationController,
    UserSettingsController
};
use App\Http\Controllers\ChatbotController;

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
 
use App\Http\Controllers\Api\BookingsController;

// ----------------------------
// Public Routes
// ----------------------------

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/chatbot', [ChatbotController::class, 'sendMessage']);
Route::get('/chatbot/quick-responses', [ChatbotController::class, 'getQuickResponses']);
Route::get('/chatbot/faqs', [ChatbotController::class, 'getFAQs']);

Route::post('signup', [UserAuthController::class, 'signup']);
Route::post('login', [UserAuthController::class, 'login']);

Route::post('admin/login', [AdminAuthController::class, 'login']);
Route::post('admin/signup', [AdminAuthController::class, 'signup']); // Add admin signup route for testing

Route::get('services', [ServicesController::class, 'home_card']);
Route::get('services/{services_id}', [ServicesController::class, 'show']);
Route::get('service/{services_id}', [ServicePageController::class, 'show']);
Route::get('services/category/{category}', [ServicesController::class, 'getByCategory']);
Route::post('/contact', [ContactController::class, 'store']);
Route::get('search', [SearchController::class, 'search']);

/*
|--------------------------------------------------------------------------
| Protected Routes – USER
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [UserAuthController::class, 'logout']);
    Route::get('me', [UserAuthController::class, 'me']);
    Route::post('refresh', [UserAuthController::class, 'refresh']);

    Route::get('profile', [ProfileController::class, 'getProfile']);
    Route::put('profile/update', [ProfileController::class, 'updateProfile']);
    Route::put('profile/update-picture', [ProfileController::class, 'updateProfilePicture']);
    Route::post('upload-profile', [ProfileController::class, 'upload']);
    Route::get('profile-pictures', [ProfileController::class, 'show_profile_picture']);

    // ✅ Settings Routes
    Route::prefix('settings')->group(function () {
        Route::put('update-name', [UserSettingsController::class, 'updateName']);
        Route::put('update-email', [UserSettingsController::class, 'updateEmail']);
        Route::put('update-password', [UserSettingsController::class, 'updatePassword']); // ← Correct path
        Route::delete('delete-account', [UserSettingsController::class, 'deleteProfile']);
    });

    Route::post('/provider-applications', [ProviderApplicationController::class, 'store']);
    Route::post('services', [ServicesController::class, 'addService']);
    Route::post('addBookings', [ServicePageController::class, 'addBookings']);
    Route::get('bookings', [ServicePageController::class, 'getBookings']);
    Route::get('my-bookings', [ServicePageController::class, 'getUserBookings']);

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('notifications/mark-read', [NotificationController::class, 'markRead']);
    Route::post('notifications/{id}/mark-read', [NotificationController::class, 'markSingleRead']);
    Route::post('notifications', [NotificationController::class, 'store']);

    Route::delete('services/{services_id}', [ServicesController::class, 'destroy']);
    Route::get('my-services', [ServicesController::class, 'myServices']);

    Route::post('addReview', [ReviewController::class, 'addReview']);
    Route::post('addPayment', [PaymentController::class, 'addPayment']);



    Route::get('provider-bookings', [BookingsController::class, 'index']); // bookings for services created by logged-in user
Route::put('bookings/{id}/cancel', [BookingsController::class, 'cancel']);
Route::put('bookings/{id}/available', [BookingsController::class, 'available']);
Route::put('bookings/{id}/confirm', [BookingsController::class, 'confirm']);
Route::put('bookings/{id}/complete', [BookingsController::class, 'complete']);
Route::put('provider/services/mark-all-available', [BookingsController::class, 'markAllAvailable']);
    
});

/*
|--------------------------------------------------------------------------
| Protected Routes – ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')->group(function () {
    Route::get('admin/profile', [AdminProfileController::class, 'getProfile']);
    Route::get('admin-services', [ServicesController::class, 'adminServices']);
    Route::delete('admin/services/{services_id}', [ServicesController::class, 'destroy']);
    Route::post('admin/logout', [AdminAuthController::class, 'logout']);
    Route::get('/admin/applications', [ProviderApplicationController::class, 'index']);
    Route::post('/admin/applications/{application}/approve', [ProviderApplicationController::class, 'approve']);
    Route::post('/admin/applications/{application}/reject', [ProviderApplicationController::class, 'reject']);
});
