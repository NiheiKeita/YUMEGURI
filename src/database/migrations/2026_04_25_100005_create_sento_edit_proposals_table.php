<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sento_edit_proposals', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('sento_id')->constrained('sentos')->cascadeOnDelete();
            $table->foreignId('proposed_by')->constrained('users')->cascadeOnDelete();
            $table->json('changes');
            $table->string('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->index(['status', 'created_at']);
            $table->index('sento_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sento_edit_proposals');
    }
};
