<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {

            // Cek satu-satu, kalau kolom BELUM ada baru bikin
            if (!Schema::hasColumn('produks', 'brand_id')) {
                $table->foreignId('brand_id')->nullable()->after('id')
                      ->constrained('brands')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('produks', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('brand_id')
                      ->constrained('categories')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('produks', 'subcategory_id')) {
                $table->foreignId('subcategory_id')->nullable()->after('category_id')
                      ->constrained('sub_categories')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('produks', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }

            if (!Schema::hasColumn('produks', 'in_stock')) {
                $table->boolean('in_stock')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['subcategory_id']);
            $table->dropColumn(['brand_id', 'category_id', 'subcategory_id', 'is_active', 'in_stock']);
        });
    }
};
