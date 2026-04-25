<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Collection: users
 *
 * Fields:
 *   _id, name, email, password_hash,
 *   gender, age, region, education_level,
 *   created_at, last_login
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mongodb')->create('users', function (Blueprint $collection) {
            $collection->string('name');
            $collection->string('email')->unique();
            $collection->string('password_hash');

            $collection->string('gender')->nullable();
            $collection->integer('age')->nullable();
            $collection->string('region')->nullable();
            $collection->string('education_level')->nullable();

            $collection->timestamp('last_login')->nullable();
            $collection->timestamps(); // created_at saja yg dipakai (updated_at boleh ada, tidak wajib)
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('users');
    }
};
