<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminUsersCollection extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mongodb')->create('admin_users', function (Blueprint $collection) {

            $collection->id();

            $collection->string('name');
            $collection->string('email')->unique();
            $collection->string('password');

            $collection->string('role')->default('admin');

            $collection->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users_collection');
    }
};
