<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('business_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade'); // FK
            $table->string('business_name');
            $table->string('owner_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('gst_number')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();

            $table->unique('store_id'); // 1:1 relationship, each store has one business detail
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_details');
    }
};
