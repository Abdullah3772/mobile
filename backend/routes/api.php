<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\ShopFollowController;
use App\Http\Controllers\Api\PublicApi\HomeController;
use App\Http\Controllers\Api\PublicApi\PublicProductController;
use App\Http\Controllers\Api\PublicApi\PublicShopController;
use App\Http\Controllers\Api\Admin\AdminShopController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\BrandController;
use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\Admin\BannerController;
use App\Http\Controllers\Api\Admin\AdminProductController;
use App\Http\Controllers\Api\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Api\Admin\AdminReviewController;
use App\Http\Controllers\Api\Admin\DeliveryPartnerController;
use App\Http\Controllers\Api\Admin\AdPackageController;
use App\Http\Controllers\Api\Admin\AnalyticsController;
use App\Http\Controllers\Api\Shop\ShopRegistrationController;
use App\Http\Controllers\Api\Shop\ShopProductController;
use App\Http\Controllers\Api\Shop\ShopReservationController;
use App\Http\Controllers\Api\Shop\ShopOfferController;
use App\Http\Controllers\Api\Shop\ShopAnalyticsController;
use App\Http\Controllers\Api\Shop\ShopAdvertisementController;

// Public routes
Route::prefix('v1')->group(function () {

    // Auth
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Home
    Route::get('home', [HomeController::class, 'index']);

    // Public Categories & Brands
    Route::get('categories', [CategoryController::class, 'all']);
    Route::get('brands', [BrandController::class, 'index']);

    // Public Products
    Route::get('products', [PublicProductController::class, 'index']);
    Route::get('products/featured', [PublicProductController::class, 'featured']);
    Route::get('products/latest', [PublicProductController::class, 'latest']);
    Route::get('products/{slug}', [PublicProductController::class, 'show']);
    Route::post('products/compare', [PublicProductController::class, 'compare']);

    // Public Shops
    Route::get('shops', [PublicShopController::class, 'index']);
    Route::get('shops/top-rated', [PublicShopController::class, 'topRated']);
    Route::get('shops/verified', [PublicShopController::class, 'verified']);
    Route::get('shops/{slug}', [PublicShopController::class, 'show']);

    // Shop Reviews (public)
    Route::get('reviews/shop/{shopId}', [ReviewController::class, 'shopReviews']);

    // Announcements (public)
    Route::get('announcements', [AnnouncementController::class, 'active']);

    // Authenticated routes
    Route::middleware('jwt.auth')->group(function () {

        // Auth
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::put('auth/profile', [AuthController::class, 'updateProfile']);
        Route::put('auth/password', [AuthController::class, 'changePassword']);

        // Wishlist
        Route::get('wishlist', [WishlistController::class, 'index']);
        Route::post('wishlist/toggle', [WishlistController::class, 'toggle']);
        Route::get('wishlist/check/{productId}', [WishlistController::class, 'check']);

        // Reservations
        Route::get('reservations', [ReservationController::class, 'index']);
        Route::post('reservations', [ReservationController::class, 'store']);
        Route::put('reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);

        // Reviews
        Route::post('reviews', [ReviewController::class, 'store']);

        // Chat
        Route::get('chat/conversations', [ChatController::class, 'conversations']);
        Route::post('chat/start', [ChatController::class, 'startConversation']);
        Route::get('chat/{conversationId}/messages', [ChatController::class, 'messages']);
        Route::post('chat/{conversationId}/messages', [ChatController::class, 'sendMessage']);
        Route::get('chat/unread-count', [ChatController::class, 'unreadCount']);

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::put('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);

        // Complaints
        Route::get('complaints', [ComplaintController::class, 'index']);
        Route::post('complaints', [ComplaintController::class, 'store']);

        // Shop Follow
        Route::post('shops/follow', [ShopFollowController::class, 'toggle']);
        Route::get('shops/following', [ShopFollowController::class, 'following']);
        Route::get('shops/{shopId}/following', [ShopFollowController::class, 'check']);

        // Shop Registration
        Route::post('shop/register', [ShopRegistrationController::class, 'register']);
        Route::get('shop/my-shop', [ShopRegistrationController::class, 'myShop']);
        Route::put('shop/update', [ShopRegistrationController::class, 'updateShop']);

        // Shop Owner routes
        Route::middleware('shop.owner')->prefix('shop')->group(function () {
            // Products
            Route::get('products', [ShopProductController::class, 'index']);
            Route::post('products', [ShopProductController::class, 'store']);
            Route::get('products/{product}', [ShopProductController::class, 'show']);
            Route::put('products/{product}', [ShopProductController::class, 'update']);
            Route::delete('products/{product}', [ShopProductController::class, 'destroy']);
            Route::put('products/{product}/sold', [ShopProductController::class, 'markSold']);
            Route::put('products/{product}/stock', [ShopProductController::class, 'updateStock']);

            // Reservations
            Route::get('reservations', [ShopReservationController::class, 'index']);
            Route::put('reservations/{reservation}/accept', [ShopReservationController::class, 'accept']);
            Route::put('reservations/{reservation}/reject', [ShopReservationController::class, 'reject']);
            Route::put('reservations/{reservation}/complete', [ShopReservationController::class, 'complete']);

            // Offers
            Route::get('offers', [ShopOfferController::class, 'index']);
            Route::post('offers', [ShopOfferController::class, 'store']);
            Route::put('offers/{offer}', [ShopOfferController::class, 'update']);
            Route::delete('offers/{offer}', [ShopOfferController::class, 'destroy']);

            // Analytics
            Route::get('analytics', [ShopAnalyticsController::class, 'dashboard']);

            // Advertisements
            Route::get('ad-packages', [ShopAdvertisementController::class, 'packages']);
            Route::get('my-ads', [ShopAdvertisementController::class, 'myAds']);
            Route::post('ads/purchase', [ShopAdvertisementController::class, 'purchase']);
        });

        // Admin routes
        Route::middleware('admin')->prefix('admin')->group(function () {
            // Dashboard
            Route::get('dashboard', [AnalyticsController::class, 'dashboard']);
            Route::get('analytics/top-shops', [AnalyticsController::class, 'topShops']);
            Route::get('analytics/most-viewed', [AnalyticsController::class, 'mostViewedProducts']);
            Route::get('analytics/recent-reservations', [AnalyticsController::class, 'recentReservations']);
            Route::get('analytics/users', [AnalyticsController::class, 'userStats']);

            // Shops
            Route::get('shops', [AdminShopController::class, 'index']);
            Route::get('shops/stats', [AdminShopController::class, 'stats']);
            Route::get('shops/{shop}', [AdminShopController::class, 'show']);
            Route::put('shops/{shop}/approve', [AdminShopController::class, 'approve']);
            Route::put('shops/{shop}/reject', [AdminShopController::class, 'reject']);
            Route::put('shops/{shop}/suspend', [AdminShopController::class, 'suspend']);
            Route::put('shops/{shop}/verify', [AdminShopController::class, 'verify']);

            // Categories
            Route::apiResource('categories', CategoryController::class);

            // Brands
            Route::apiResource('brands', BrandController::class);

            // Products
            Route::get('products', [AdminProductController::class, 'index']);
            Route::put('products/{product}/feature', [AdminProductController::class, 'feature']);
            Route::delete('products/{product}', [AdminProductController::class, 'destroy']);

            // Reviews
            Route::get('reviews', [AdminReviewController::class, 'index']);
            Route::put('reviews/{review}/approve', [AdminReviewController::class, 'approve']);
            Route::put('reviews/{review}/reject', [AdminReviewController::class, 'reject']);
            Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy']);

            // Complaints
            Route::get('complaints', [AdminComplaintController::class, 'index']);
            Route::get('complaints/{complaint}', [AdminComplaintController::class, 'show']);
            Route::put('complaints/{complaint}/respond', [AdminComplaintController::class, 'respond']);

            // Announcements
            Route::apiResource('announcements', AnnouncementController::class);

            // Banners
            Route::apiResource('banners', BannerController::class);

            // Delivery Partners
            Route::apiResource('delivery-partners', DeliveryPartnerController::class);

            // Ad Packages
            Route::apiResource('ad-packages', AdPackageController::class);
            Route::get('advertisements', [AdPackageController::class, 'advertisements']);
            Route::put('advertisements/{advertisement}/approve', [AdPackageController::class, 'approveAd']);
            Route::put('advertisements/{advertisement}/reject', [AdPackageController::class, 'rejectAd']);
        });
    });
});
