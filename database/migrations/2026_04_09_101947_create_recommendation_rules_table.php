<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('variable');
            $table->string('operator');
            $table->float('value');
            $table->text('recommendation');
            $table->enum('priority', ['high', 'medium', 'low']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        }); // ⚠️ INI WAJIB ADA
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_rules');
    }
};