<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prediction_batches', function (Blueprint $table) {
            $table->id();
            $table->string('status', 50)->default('pending');
            $table->integer('total_pairs')->default(0);
            $table->integer('processed_pairs')->default(0);
            $table->integer('total_predictions')->default(0);
            $table->text('ai_insight')->nullable();
            $table->timestamp('ai_insight_generated_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediction_batches');
    }
};
