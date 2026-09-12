<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            // brand_id — belum ada, aman ditambah
            $table->foreignId('brand_id')->nullable()->after('id')
                  ->constrained('brands')->cascadeOnDelete();

            // category_id SUDAH ADA → cuma tambah FK
            $table->foreign('category_id')->references('id')
                  ->on('categories')->cascadeOnDelete();

            // subcategory_id SUDAH ADA → cuma tambah FK
            $table->foreign('subcategory_id')->references('id')
                  ->on('sub_categories')->cascadeOnDelete();

            // Kolom baru
            $table->boolean('is_active')->default(true);
            $table->boolean('in_stock')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['subcategory_id']);
            $table->dropColumn(['brand_id', 'is_active', 'in_stock']);
        });
    }
};
