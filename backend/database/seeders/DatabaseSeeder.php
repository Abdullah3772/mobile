<?php

namespace Database\Seeders;

use App\Models\AdPackage;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@smartdeals.lk',
            'password' => 'password123',
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        // Categories
        $smartphones = Category::create(['name' => 'Smartphones', 'slug' => 'smartphones', 'icon' => 'smartphone', 'sort_order' => 1]);
        Category::create(['name' => 'Brand New', 'slug' => 'brand-new', 'parent_id' => $smartphones->id, 'sort_order' => 1]);
        Category::create(['name' => 'Used', 'slug' => 'used', 'parent_id' => $smartphones->id, 'sort_order' => 2]);
        Category::create(['name' => 'Refurbished', 'slug' => 'refurbished', 'parent_id' => $smartphones->id, 'sort_order' => 3]);

        $accessories = Category::create(['name' => 'Accessories', 'slug' => 'accessories', 'icon' => 'headphones', 'sort_order' => 2]);
        Category::create(['name' => 'Chargers', 'slug' => 'chargers', 'parent_id' => $accessories->id, 'sort_order' => 1]);
        Category::create(['name' => 'Cases', 'slug' => 'cases', 'parent_id' => $accessories->id, 'sort_order' => 2]);
        Category::create(['name' => 'Earbuds', 'slug' => 'earbuds', 'parent_id' => $accessories->id, 'sort_order' => 3]);
        Category::create(['name' => 'Headphones', 'slug' => 'headphones', 'parent_id' => $accessories->id, 'sort_order' => 4]);
        Category::create(['name' => 'Smart Watches', 'slug' => 'smart-watches', 'parent_id' => $accessories->id, 'sort_order' => 5]);
        Category::create(['name' => 'Power Banks', 'slug' => 'power-banks', 'parent_id' => $accessories->id, 'sort_order' => 6]);

        Category::create(['name' => 'Tablets', 'slug' => 'tablets', 'icon' => 'tablet', 'sort_order' => 3]);
        Category::create(['name' => 'Laptops', 'slug' => 'laptops', 'icon' => 'laptop', 'sort_order' => 4]);
        Category::create(['name' => 'Gaming Devices', 'slug' => 'gaming-devices', 'icon' => 'gamepad', 'sort_order' => 5]);
        Category::create(['name' => 'Spare Parts', 'slug' => 'spare-parts', 'icon' => 'wrench', 'sort_order' => 6]);

        // Brands
        $brandNames = ['Apple', 'Samsung', 'Google', 'OnePlus', 'Xiaomi', 'Huawei', 'Oppo', 'Vivo', 'Realme', 'Nokia', 'Sony', 'LG', 'Motorola', 'Nothing'];
        foreach ($brandNames as $i => $name) {
            Brand::create(['name' => $name, 'slug' => Str::slug($name), 'sort_order' => $i + 1]);
        }

        // Ad Packages
        AdPackage::create([
            'name' => 'Silver', 'slug' => 'silver', 'description' => 'Basic visibility boost',
            'price' => 5000, 'duration_days' => 7, 'max_products' => 3,
        ]);
        AdPackage::create([
            'name' => 'Gold', 'slug' => 'gold', 'description' => 'Enhanced visibility with homepage placement',
            'price' => 15000, 'duration_days' => 30, 'homepage_banner' => true,
            'top_search_placement' => true, 'max_products' => 10,
        ]);
        AdPackage::create([
            'name' => 'Platinum', 'slug' => 'platinum', 'description' => 'Maximum visibility with all features',
            'price' => 30000, 'duration_days' => 30, 'homepage_banner' => true,
            'top_search_placement' => true, 'featured_badge' => true, 'max_products' => 50,
        ]);

        // Sample Shop Owner
        $shopOwner = User::create([
            'name' => 'MobileZone Owner',
            'email' => 'shop@smartdeals.lk',
            'password' => 'password123',
            'role' => 'shop_owner',
            'phone' => '0771234567',
            'district' => 'Colombo',
        ]);

        $shop = Shop::create([
            'user_id' => $shopOwner->id,
            'name' => 'MobileZone Colombo',
            'slug' => 'mobilezone-colombo',
            'owner_name' => 'Kamal Perera',
            'nic' => '200012345678',
            'address' => '123 Galle Road, Colombo 03',
            'district' => 'Colombo',
            'phone' => '0771234567',
            'whatsapp' => '0771234567',
            'email' => 'shop@smartdeals.lk',
            'about' => 'Leading mobile phone shop in Colombo with 10+ years of experience. We sell brand new and certified pre-owned phones.',
            'status' => 'approved',
            'is_verified' => true,
            'rating' => 4.5,
            'total_reviews' => 25,
            'opening_hours' => [
                'monday' => '9:00 AM - 7:00 PM',
                'tuesday' => '9:00 AM - 7:00 PM',
                'wednesday' => '9:00 AM - 7:00 PM',
                'thursday' => '9:00 AM - 7:00 PM',
                'friday' => '9:00 AM - 7:00 PM',
                'saturday' => '9:00 AM - 5:00 PM',
                'sunday' => 'Closed',
            ],
        ]);

        // Sample Shop 2
        $shopOwner2 = User::create([
            'name' => 'PhoneWorld Owner',
            'email' => 'shop2@smartdeals.lk',
            'password' => 'password123',
            'role' => 'shop_owner',
            'phone' => '0769876543',
            'district' => 'Kandy',
        ]);

        $shop2 = Shop::create([
            'user_id' => $shopOwner2->id,
            'name' => 'PhoneWorld Kandy',
            'slug' => 'phoneworld-kandy',
            'owner_name' => 'Nuwan Silva',
            'nic' => '199812345678',
            'address' => '45 Peradeniya Road, Kandy',
            'district' => 'Kandy',
            'phone' => '0769876543',
            'whatsapp' => '0769876543',
            'status' => 'approved',
            'is_verified' => true,
            'rating' => 4.2,
            'total_reviews' => 18,
        ]);

        // Sample Products
        $apple = Brand::where('slug', 'apple')->first();
        $samsung = Brand::where('slug', 'samsung')->first();
        $google = Brand::where('slug', 'google')->first();

        $products = [
            [
                'shop_id' => $shop->id, 'category_id' => $smartphones->id, 'brand_id' => $apple->id,
                'title' => 'iPhone 15 Pro Max 256GB', 'slug' => 'iphone-15-pro-max-256gb',
                'model' => 'iPhone 15 Pro Max', 'condition' => 'brand_new', 'storage' => '256GB',
                'ram' => '8GB', 'color' => 'Natural Titanium', 'price' => 485000,
                'warranty' => '1 Year', 'warranty_type' => 'Official Apple Warranty',
                'network_type' => '5G', 'trcsl_approved' => true, 'box_available' => true,
                'cash_price' => 475000, 'card_price' => 485000, 'emi_available' => true,
                'camera' => '48MP + 12MP + 12MP', 'battery' => '4422mAh', 'processor' => 'A17 Pro',
                'screen_size' => '6.7"', 'five_g_support' => true, 'is_featured' => true,
                'stock_quantity' => 5,
            ],
            [
                'shop_id' => $shop->id, 'category_id' => $smartphones->id, 'brand_id' => $samsung->id,
                'title' => 'Samsung Galaxy S25 Ultra 512GB', 'slug' => 'samsung-galaxy-s25-ultra-512gb',
                'model' => 'Galaxy S25 Ultra', 'condition' => 'brand_new', 'storage' => '512GB',
                'ram' => '12GB', 'color' => 'Titanium Black', 'price' => 425000,
                'discount_price' => 399000, 'warranty' => '1 Year', 'warranty_type' => 'Distributor Warranty',
                'network_type' => '5G', 'trcsl_approved' => true, 'box_available' => true,
                'emi_available' => true, 'camera' => '200MP + 50MP + 12MP + 10MP',
                'battery' => '5000mAh', 'processor' => 'Snapdragon 8 Elite', 'screen_size' => '6.9"',
                'five_g_support' => true, 'is_featured' => true, 'stock_quantity' => 3,
            ],
            [
                'shop_id' => $shop->id, 'category_id' => $smartphones->id, 'brand_id' => $apple->id,
                'title' => 'iPhone 14 128GB - Used', 'slug' => 'iphone-14-128gb-used',
                'model' => 'iPhone 14', 'condition' => 'used', 'storage' => '128GB',
                'ram' => '6GB', 'color' => 'Blue', 'price' => 180000,
                'discount_price' => 165000, 'battery_health' => '89%', 'scratches' => 'Minor scratches on back',
                'face_id_working' => true, 'original_display' => true,
                'trcsl_approved' => true, 'camera' => '12MP + 12MP', 'battery' => '3279mAh',
                'processor' => 'A15 Bionic', 'screen_size' => '6.1"', 'stock_quantity' => 1,
            ],
            [
                'shop_id' => $shop2->id, 'category_id' => $smartphones->id, 'brand_id' => $google->id,
                'title' => 'Google Pixel 9 Pro 256GB', 'slug' => 'google-pixel-9-pro-256gb',
                'model' => 'Pixel 9 Pro', 'condition' => 'brand_new', 'storage' => '256GB',
                'ram' => '16GB', 'color' => 'Obsidian', 'price' => 320000,
                'warranty' => '1 Year', 'network_type' => '5G', 'trcsl_approved' => true,
                'box_available' => true, 'camera' => '50MP + 48MP + 48MP', 'battery' => '4700mAh',
                'processor' => 'Tensor G4', 'screen_size' => '6.3"', 'five_g_support' => true,
                'is_featured' => true, 'stock_quantity' => 4,
            ],
            [
                'shop_id' => $shop2->id, 'category_id' => $smartphones->id, 'brand_id' => $samsung->id,
                'title' => 'Samsung Galaxy A55 5G 128GB', 'slug' => 'samsung-galaxy-a55-5g-128gb',
                'model' => 'Galaxy A55', 'condition' => 'brand_new', 'storage' => '128GB',
                'ram' => '8GB', 'color' => 'Awesome Iceblue', 'price' => 95000,
                'discount_price' => 89000, 'warranty' => '1 Year', 'network_type' => '5G',
                'trcsl_approved' => true, 'box_available' => true, 'emi_available' => true,
                'camera' => '50MP + 12MP + 5MP', 'battery' => '5000mAh',
                'processor' => 'Exynos 1480', 'screen_size' => '6.6"', 'five_g_support' => true,
                'stock_quantity' => 10,
            ],
            [
                'shop_id' => $shop->id, 'category_id' => $smartphones->id, 'brand_id' => $apple->id,
                'title' => 'iPhone 13 Mini 128GB - Refurbished', 'slug' => 'iphone-13-mini-128gb-refurbished',
                'model' => 'iPhone 13 Mini', 'condition' => 'refurbished', 'storage' => '128GB',
                'ram' => '4GB', 'color' => 'Midnight', 'price' => 120000,
                'battery_health' => '95%', 'face_id_working' => true, 'original_display' => true,
                'warranty' => '3 Months', 'warranty_type' => 'Shop Warranty',
                'camera' => '12MP + 12MP', 'battery' => '2438mAh', 'processor' => 'A15 Bionic',
                'screen_size' => '5.4"', 'stock_quantity' => 2,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Sample Customer
        User::create([
            'name' => 'Test Customer',
            'email' => 'customer@smartdeals.lk',
            'password' => 'password123',
            'role' => 'customer',
            'phone' => '0751112233',
            'district' => 'Colombo',
        ]);

        // Update shop product counts
        $shop->update(['total_products' => $shop->products()->count()]);
        $shop2->update(['total_products' => $shop2->products()->count()]);

        // Sample Banners
        Banner::create([
            'title' => 'Welcome to SmartDeals.lk',
            'image' => 'banners/hero-1.jpg',
            'position' => 'hero',
            'sort_order' => 1,
        ]);
    }
}
