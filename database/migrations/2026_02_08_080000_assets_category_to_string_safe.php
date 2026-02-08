<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Safe for existing data: adds category string, backfills from categories if needed, removes category_id.
     */
    public function up(): void
    {
        // Recover from partial run (e.g. SQLite index conflict left assets_old behind)
        if (Schema::hasTable('assets_old')) {
            Schema::dropIfExists('assets');
            Schema::rename('assets_old', 'assets');
        }

        $hasCategoryId = Schema::hasColumn('assets', 'category_id');
        $hasCategory = Schema::hasColumn('assets', 'category');

        // 1. Ensure category column exists as string(255), not null
        if (!$hasCategory) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('category', 255)->default('Uncategorized')->after('serial_number');
            });
        }

        // 2. Backfill category from categories table where we have category_id (for existing data)
        if ($hasCategoryId && Schema::hasTable('categories')) {
            $categories = DB::table('categories')->pluck('name', 'id');
            $assets = DB::table('assets')->whereNotNull('category_id')->get(['id', 'category_id', 'category']);
            foreach ($assets as $asset) {
                $name = $categories[$asset->category_id] ?? 'Uncategorized';
                if ($name && (empty($asset->category) || $asset->category === 'Uncategorized')) {
                    DB::table('assets')->where('id', $asset->id)->update(['category' => $name]);
                }
            }
        }

        // 3. Remove category_id: MySQL/PgSQL drop foreign then column; SQLite recreate table
        if ($hasCategoryId) {
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                Schema::dropIfExists('assets_old');
                Schema::rename('assets', 'assets_old');
                // SQLite keeps index names on rename; drop so new table can create same-named index
                DB::statement('DROP INDEX IF EXISTS assets_serial_number_unique');
                Schema::create('assets', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('serial_number')->unique()->nullable();
                    $table->string('category', 255);
                    $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
                    $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
                    $table->date('purchase_date')->nullable();
                    $table->date('warranty_expiry')->nullable();
                    $table->enum('status', ['active', 'maintenance', 'retired', 'lost'])->default('active');
                    $table->text('notes')->nullable();
                    $table->timestamps();
                });
                DB::statement('INSERT INTO assets (id, name, serial_number, category, location_id, assigned_to_user_id, purchase_date, warranty_expiry, status, notes, created_at, updated_at) SELECT id, name, serial_number, category, location_id, assigned_to_user_id, purchase_date, warranty_expiry, status, notes, created_at, updated_at FROM assets_old');
                Schema::drop('assets_old');
            } else {
                if (in_array($driver, ['mysql', 'pgsql'])) {
                    Schema::table('assets', function (Blueprint $table) {
                        $table->dropForeign(['category_id']);
                    });
                }
                Schema::table('assets', function (Blueprint $table) {
                    $table->dropColumn('category_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('assets', 'category_id')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->after('serial_number')->constrained('categories')->nullOnDelete();
            });
        }
        if (Schema::hasColumn('assets', 'category')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};
