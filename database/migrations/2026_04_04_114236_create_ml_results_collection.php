<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMlResultsCollection extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mongodb')->create('ml_results', function (Blueprint $collection) {

            $collection->id();

            $collection->foreignId('user_id');
            $collection->foreignId('questionnaire_id');

            $collection->float('focus_score');
            $collection->float('productivity_score');
            $collection->float('digital_dependence_score');

            $collection->boolean('high_risk_flag')->default(false);

            $collection->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ml_results_collection');
    }
};
