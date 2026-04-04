<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalyticsLogsCollection extends Migration
{
    public function up()
    {
        Schema::connection('mongodb')->create('analytics_logs', function (Blueprint $collection) {

            $collection->id();

            // relasi ke user
            $collection->foreignId('user_id');

            // rata-rata fokus 7 hari terakhir
            $collection->float('avg_focus_7_days')->nullable();

            // persentase perubahan fokus
            // contoh: -15 berarti turun 15%
            $collection->float('focus_change_percentage')->nullable();

            // rata-rata produktivitas 7 hari terakhir
            $collection->float('avg_productivity_7_days')->nullable();

            // waktu dibuat insight
            $collection->timestamp('created_at')->nullable();

        });
    }

    public function down()
    {
        Schema::connection('mongodb')->dropIfExists('analytics_logs');
    }
};