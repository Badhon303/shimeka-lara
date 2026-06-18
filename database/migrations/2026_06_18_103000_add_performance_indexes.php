<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('is_new');
            $table->index('category_id');
            $table->index('price');
            $table->index('brand');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_status');
            $table->index('user_id');
            $table->index('order_number');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('type');
            $table->index('is_active');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('position');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('is_approved');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['is_new']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['price']);
            $table->dropIndex(['brand']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['order_number']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['position']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['is_approved']);
        });
    }
};
