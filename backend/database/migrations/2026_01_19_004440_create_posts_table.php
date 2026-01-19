<?php

declare(strict_types=1);

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
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('excerpt');
                $table->longText('content');
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                $table->string('featured_image')->nullable();
                $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
                $table->integer('views_count')->default(0);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
