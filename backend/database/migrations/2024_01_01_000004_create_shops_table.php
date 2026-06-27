<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('owner_name');
            $table->string('business_registration_number')->nullable();
            $table->string('nic');
            $table->text('address');
            $table->string('district');
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('google_map_lat')->nullable();
            $table->string('google_map_lng')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->text('about')->nullable();
            $table->json('opening_hours')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->enum('subscription_plan', ['free', 'pro', 'premium', 'enterprise'])->default('free');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_products')->default(0);
            $table->integer('total_views')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shop_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_documents');
        Schema::dropIfExists('shops');
    }
};
