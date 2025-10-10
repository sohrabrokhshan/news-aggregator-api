<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Making these fields nullable because I got null from newsapi articles in some of its articles.
        Schema::table('articles', function (Blueprint $table) {
            $table->string('headline')->nullable()->change();
            $table->text('content')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('headline')->nullable(false)->change();
            $table->text('content')->nullable(false)->change();
        });
    }
};
