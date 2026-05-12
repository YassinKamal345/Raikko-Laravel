<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (
            Blueprint $table
        ) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Shopify
            |--------------------------------------------------------------------------
            */

            $table->string(
                'shopify_order_id'
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->string('status')
                ->default('pending');

            $table->string(
                'payment_status'
            )->default('pending');

            /*
            |--------------------------------------------------------------------------
            | Prices
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'subtotal',
                10,
                2
            );

            $table->decimal(
                'shipping',
                10,
                2
            )->default(0);

            $table->decimal(
                'total',
                10,
                2
            );

            /*
            |--------------------------------------------------------------------------
            | Shipping
            |--------------------------------------------------------------------------
            */

            $table->string(
                'tracking_number'
            )->nullable();

            $table->string(
                'customer_name'
            )->nullable();

            $table->string(
                'customer_email'
            )->nullable();

            $table->string(
                'address'
            )->nullable();

            $table->string(
                'city'
            )->nullable();

            $table->string(
                'postal_code'
            )->nullable();

            $table->string(
                'country'
            )->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};