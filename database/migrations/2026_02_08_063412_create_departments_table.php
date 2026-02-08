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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            // We reference users table, but it might not handle the foreign key constraint perfectly if user is created later, 
            // but users table exists before this migration so it's fine.
            // head_user_id is nullable.
            $table->foreignId('head_user_id')->nullable()->constrained('users')->nullOnDelete(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
