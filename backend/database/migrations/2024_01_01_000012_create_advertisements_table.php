<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('duration_days');
            $table->boolean('homepage_banner')->default(false);
            $table->boolean('top_search_placement')->default(false);
            $table->boolean('featured_badge')->default(false);
            $table->integer('max_products')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('ad_package_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('banner_image')->nullable();
            $table->string('link')->nullable();
            $table->enum('position', ['homepage_top', 'homepage_middle', 'sidebar', 'search_top', 'category_top'])->default('homepage_top');
            $table->enum('status', ['pending', 'active', 'expired', 'rejected'])->default('pending');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->integer('clicks')->default(0);
            $table->integer('impressions')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('ad_packages');
    }
};
