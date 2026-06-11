<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500);
            $table->string('url', 1000)->unique();
            $table->string('source', 100);
            $table->timestamp('published_at')->nullable();
            $table->text('summary')->nullable();
            $table->json('commodity_tags')->nullable();
            $table->boolean('is_relevant')->default(true);
            $table->timestamps();

            $table->index(['published_at', 'is_relevant']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
