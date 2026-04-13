<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * FILE: database/migrations/2026_04_13_000004_drop_unused_collections.php
 *
 * Drop collection / table yang tidak digunakan lagi:
 *   - recommendations         → sudah di-embed ke ml_results (migration 000003)
 *   - admin_otps              → diganti ENV
 *   - personal_access_tokens  → tidak dipakai (dihapus)
 *   - otp_sessions            → diganti ENV
 *
 * ⚠️  Pastikan migration 000003 sudah berhasil dijalankan sebelum ini!
 */
return new class extends Migration
{
    public function up(): void
    {
        // MongoDB collections
        Schema::connection('mongodb')->dropIfExists('recommendations');
        Schema::connection('mongodb')->dropIfExists('admin_otps');
        Schema::connection('mongodb')->dropIfExists('otp_sessions');

        // MySQL / default table (personal_access_tokens dibuat oleh Laravel Sanctum)
        Schema::dropIfExists('personal_access_tokens');
    }

    public function down(): void
    {
        // Re-create recommendations (kosong — data sudah ada di ml_results)
        Schema::connection('mongodb')->create('recommendations', function ($collection) {
            $collection->id();
            $collection->foreignId('result_id');
            $collection->json('recommendations');
            $collection->timestamps();
        });

        // Re-create admin_otps
        Schema::connection('mongodb')->create('admin_otps', function ($collection) {
            $collection->id();
            $collection->string('admin_id')->index();
            $collection->string('email')->index();
            $collection->string('otp_code');
            $collection->integer('attempts')->default(0);
            $collection->timestamp('expired_at')->index();
            $collection->boolean('verified')->default(false);
            $collection->timestamps();
        });

        // Re-create otp_sessions
        Schema::connection('mongodb')->create('otp_sessions', function ($collection) {
            $collection->id();
            $collection->string('email')->index();
            $collection->string('otp_code');
            $collection->timestamp('expired_at');
            $collection->boolean('verified')->default(false);
            $collection->string('ip_address')->nullable();
            $collection->timestamps();
        });

        // Re-create personal_access_tokens (Sanctum default)
        Schema::create('personal_access_tokens', function ($table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }
};
