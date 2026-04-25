<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sento_photos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('sento_id')->constrained('sentos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('review_id')->nullable()
                ->constrained('sento_reviews')->nullOnDelete();
            $table->string('path');
            $table->enum('category', ['exterior', 'interior', 'locker', 'other'])->default('other');
            $table->string('caption')->nullable();

            $table->index(['sento_id', 'category']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sento_photos');
    }
};
