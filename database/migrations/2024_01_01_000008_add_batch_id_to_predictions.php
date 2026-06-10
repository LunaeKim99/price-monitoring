<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->foreignId('prediction_batch_id')
                ->nullable()
                ->after('model_name')
                ->constrained('prediction_batches')
                ->nullOnDelete();

            $table->index('prediction_batch_id');
        });
    }

    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            $table->dropForeign(['prediction_batch_id']);
            $table->dropIndex(['prediction_batch_id']);
            $table->dropColumn('prediction_batch_id');
        });
    }
};
