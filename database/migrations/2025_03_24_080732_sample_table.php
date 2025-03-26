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
        Schema::create('sample_table', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('title'); // Post title
            $table->text('content'); // Full content
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sample_table', function (Blueprint $table) {
            //
        });
    }
};
