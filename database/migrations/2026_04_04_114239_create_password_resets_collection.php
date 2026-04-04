<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordResetsCollection extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mongodb')->create('password_resets', function (Blueprint $collection) {

            $collection->id();

            $collection->string('email');
            $collection->string('otp_code');

            $collection->timestamp('expired_at');
            $collection->boolean('verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_resets_collection');
    }
};
