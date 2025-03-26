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
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('title'); // Post title
            $table->text('content'); // Full content
            $table->text('excerpt')->nullable(); // Short description
            $table->string('image')->nullable(); // Image URL or path
            $table->json('tags')->nullable(); // Store multiple tags as JSON
            $table->string('meta_title')->nullable(); // SEO title
            $table->text('meta_description')->nullable(); // SEO description
            $table->enum('publish_type', ['draft', 'scheduled', 'published'])->default('draft'); // Publish status
            $table->timestamp('publish_datetime')->nullable(); // Scheduled publish time
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Author
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); // Last editor
            $table->timestamp('created_at')->useCurrent(); // created at
            $table->timestamp('updated_at')->useCurrent(); // updated at
            $table->timestamp('published')->boolean()->default(false);
            $table->softDeletes(); // Deleted at (Soft delete)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
