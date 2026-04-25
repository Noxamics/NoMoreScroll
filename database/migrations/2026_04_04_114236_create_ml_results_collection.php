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
return new class extends Migration {
    public function up(): void
    {
        Schema::connection('mongodb')->create('ml_results', function (Blueprint $collection) {
            $collection->string('user_id')->index();
            $collection->string('questionnaire_id')->index();

            $collection->float('digital_dependence_score');

            // Embedded array of string
            // Contoh: ["Kurangi waktu di media sosial", "Tidur minimal 7 jam"]
            $collection->array('recommendations');

            $collection->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('ml_results');
    }
};
