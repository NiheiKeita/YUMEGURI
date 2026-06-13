<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sento_reviews', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('sento_id')->constrained('sentos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('visited_at');
            $table->unsignedTinyInteger('rating');
            $table->text('body')->nullable();
            $table->boolean('has_sauna')->default(false);
            $table->smallInteger('sauna_temp')->nullable();
            $table->boolean('has_mizuburo')->default(false);
            $table->smallInteger('mizuburo_temp')->nullable();
            $table->json('bath_types')->nullable();
            $table->boolean('want_revisit')->default(false);
            $table->unsignedTinyInteger('crowding')->nullable();
            $table->string('best_time')->nullable();

            $table->index(['user_id', 'visited_at']);
            $table->index(['sento_id', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sento_reviews');
    }
};
