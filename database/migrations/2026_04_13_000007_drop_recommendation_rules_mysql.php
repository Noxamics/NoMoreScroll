<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * FILE: database/migrations/2026_04_13_000007_drop_recommendation_rules_mysql.php
 *
 * Drop tabel 'recommendation_rules' dari MySQL.
 * Jalankan SETELAH migration 000006 berhasil dan data
 * sudah terverifikasi pindah ke MongoDB dengan benar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('recommendation_rules');
    }

    public function down(): void
    {
        // Re-create tabel MySQL (struktur lama) jika perlu rollback
        Schema::create('recommendation_rules', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('variable');
            $table->string('operator');
            $table->float('value');
            $table->text('recommendation');
            $table->enum('priority', ['high', 'medium', 'low']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
