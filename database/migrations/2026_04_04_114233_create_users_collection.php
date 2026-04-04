<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersCollection extends Migration
{
    public function up()
    {
        Schema::connection('mongodb')->create('users', function (Blueprint $collection) {

            $collection->id();

            $collection->string('name');
            $collection->string('email')->unique();
            $collection->string('password');

            $collection->string('gender')->nullable();
            $collection->integer('age')->nullable();
            $collection->string('region')->nullable();
            $collection->string('education_level')->nullable();

            $collection->timestamps();

        });
    }

    public function down()
    {
        Schema::connection('mongodb')->dropIfExists('users');
    }
}