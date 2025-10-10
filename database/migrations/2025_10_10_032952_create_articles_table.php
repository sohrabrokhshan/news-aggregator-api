<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('title');
            $table->string('headline');
            $table->text('content');
            $table->string('image_url')->nullable();
            $table->datetime('published_at');
            $table->string('resource_url');
            $table->string('resource');
            $table->string('category_slug');
            $table->string('source_slug');
            $table->timestamps();

            $table->foreign('category_slug')->references('slug')->on('categories');
            $table->foreign('source_slug')->references('slug')->on('sources');
            $table->unique(['resource', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
