<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionnairesCollection extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mongodb')->create('questionnaires', function (Blueprint $collection) {

        $collection->id();

        $collection->foreignId('user_id');

        $collection->string('income_level')->nullable();
        $collection->string('daily_role')->nullable();

        $collection->float('device_hours_per_day')->nullable();
        $collection->integer('phone_unlocks_per_day')->nullable();
        $collection->integer('notifications_per_day')->nullable();

        $collection->integer('social_media_minutes')->nullable();
        $collection->integer('study_minutes')->nullable();
        $collection->integer('physical_activity_days')->nullable();

        $collection->float('sleep_hours')->nullable();
        $collection->float('sleep_quality')->nullable();

        $collection->float('anxiety_score')->nullable();
        $collection->float('depression_score')->nullable();
        $collection->float('stress_level')->nullable();
        $collection->float('happiness_score')->nullable();

        $collection->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaires_collection');
    }
};
