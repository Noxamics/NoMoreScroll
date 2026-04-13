<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Collection: ml_results
 *
 * Fields:
 *   _id, user_id, questionnaire_id,
 *   focus_score, productivity_score, digital_dependence_score,
 *   high_risk_flag,
 *   recommendations: [ "...", "..." ],   ← embedded, bukan relasi terpisah
 *   created_at, updated_at
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mongodb')->create('ml_results', function (Blueprint $collection) {
            $collection->string('user_id')->index();
            $collection->string('questionnaire_id')->index();

            $collection->float('focus_score');
            $collection->float('productivity_score');
            $collection->float('digital_dependence_score');

            $collection->boolean('high_risk_flag')->default(false);

            // Array of string — embedded recommendations
            // Contoh: ["Kurangi social media", "Tidur 7 jam"]
            // Di MongoDB ini otomatis tersimpan sebagai array

            $collection->timestamps(); // created_at + updated_at
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('ml_results');
    }
};
