<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 銭湯マスター。スクレイピングで初期投入し、admin の手動編集や承認済み提案で上書きされる。
     * is_manually_updated=true のレコードはスクレイピング再実行時に保持する。
     */
    public function up(): void
    {
        Schema::create('sentos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->string('name');
            $table->string('name_kana')->nullable();
            $table->string('prefecture');
            $table->string('city')->nullable();
            $table->string('address');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('phone')->nullable();
            $table->string('hours')->nullable();
            $table->string('closed_days')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->string('source_url')->nullable();
            $table->string('nearest_station')->nullable();
            $table->unsignedSmallInteger('walk_minutes')->nullable();
            $table->boolean('has_shampoo')->default(false);
            $table->boolean('has_soap')->default(false);
            $table->enum('status', ['open', 'closed_temp', 'closed_perm'])->default('open');
            $table->date('info_updated_at')->nullable();
            $table->boolean('is_manually_updated')->default(false);

            $table->index(['prefecture', 'city']);
            $table->index('status');
            // 地理検索用（並び替えを高速化）
            $table->index(['lat', 'lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sentos');
    }
};
