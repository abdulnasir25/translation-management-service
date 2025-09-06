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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('translation_key_id')->constrained()->cascadeOnDelete();
            $table->foreignId('locale_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint to prevent duplicate translations
            $table->unique(['translation_key_id', 'locale_id']);

            // Optimized indexes for queries
            $table->index(['is_active', 'locale_id']);
            $table->index(['translation_key_id', 'locale_id', 'is_active']);
            $table->fullText('content'); // For content search
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
