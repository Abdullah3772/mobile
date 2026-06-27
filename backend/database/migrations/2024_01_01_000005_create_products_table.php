<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('model')->nullable();
            $table->enum('condition', ['brand_new', 'used', 'refurbished'])->default('brand_new');
            $table->string('storage')->nullable();
            $table->string('ram')->nullable();
            $table->string('color')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->string('warranty')->nullable();
            $table->string('warranty_type')->nullable();
            $table->string('network_type')->nullable();
            $table->string('imei')->nullable();
            $table->boolean('trcsl_approved')->default(false);
            $table->boolean('box_available')->default(false);
            $table->text('accessories_included')->nullable();
            $table->integer('stock_quantity')->default(1);
            $table->string('battery_health')->nullable();
            $table->string('scratches')->nullable();
            $table->boolean('face_id_working')->nullable();
            $table->boolean('original_display')->nullable();
            $table->text('repair_history')->nullable();
            $table->decimal('cash_price', 12, 2)->nullable();
            $table->decimal('card_price', 12, 2)->nullable();
            $table->boolean('emi_available')->default(false);
            $table->string('camera')->nullable();
            $table->string('battery')->nullable();
            $table->string('processor')->nullable();
            $table->string('screen_size')->nullable();
            $table->boolean('five_g_support')->default(false);
            $table->enum('status', ['active', 'inactive', 'sold', 'reserved'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('favorites_count')->default(0);
            $table->integer('reservations_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'brand_id', 'condition', 'status']);
            $table->index(['price', 'discount_price']);
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_360')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('video_path');
            $table->string('thumbnail')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_videos');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }
};
