<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Deferred to migration 2026_02_08_080000_assets_category_to_string_safe
        // which handles SQLite (table recreate) and MySQL (dropForeign + dropColumn) safely.
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('categories');
        });
    }
};
