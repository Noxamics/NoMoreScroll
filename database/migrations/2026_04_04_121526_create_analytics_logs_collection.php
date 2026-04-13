<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Collection: analytics_logs
 *
 * Fields:
 *   _id, user_id,
 *   avg_focus_7_days, focus_change_percentage,
 *   avg_productivity_7_days,
 *   created_at
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mongodb')->create('analytics_logs', function (Blueprint $collection) {
            $collection->string('user_id')->index();

            $collection->float('avg_focus_7_days')->nullable();
            $collection->float('focus_change_percentage')->nullable();
            $collection->float('avg_productivity_7_days')->nullable();

            $collection->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('analytics_logs');
    }
};
