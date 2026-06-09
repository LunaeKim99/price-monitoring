<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commodity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 12, 2);
            $table->date('recorded_date');
            $table->string('source', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['commodity_id', 'region_id', 'recorded_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_records');
    }
};
