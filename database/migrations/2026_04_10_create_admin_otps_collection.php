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
        Schema::connection('mongodb')->create('admin_otps', function (Blueprint $collection) {
            $collection->id();
            $collection->string('admin_id')->index();
            $collection->string('email')->index();
            $collection->string('otp_code');
            $collection->integer('attempts')->default(0);
            $collection->timestamp('expired_at')->index();
            $collection->boolean('verified')->default(false);
            $collection->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('admin_otps');
    }
};
