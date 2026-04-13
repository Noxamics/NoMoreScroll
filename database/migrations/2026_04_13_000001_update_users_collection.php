<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * FILE: database/migrations/2026_04_13_000001_update_users_collection.php
 *
 * Perubahan pada collection 'users':
 *   - Rename field 'password' → 'password_hash'
 *   - Tambah field 'last_login' (nullable timestamp)
 *
 * Struktur akhir:
 *   _id, name, email, password_hash, gender, age,
 *   region, education_level, created_at, last_login
 */
return new class extends Migration
{
    public function up(): void
    {
        // Rename field 'password' → 'password_hash' di semua dokumen yang sudah ada
        // $rename adalah operator MongoDB native
        DB::connection('mongodb')
            ->collection('users')
            ->whereNotNull('password')
            ->update(['$rename' => ['password' => 'password_hash']]);

        // Tambah index pada last_login untuk query performa
        Schema::connection('mongodb')->table('users', function (Blueprint $collection) {
            $collection->timestamp('last_login')->nullable();
            $collection->index('last_login');
        });
    }

    public function down(): void
    {
        // Balik rename: password_hash → password
        DB::connection('mongodb')
            ->collection('users')
            ->whereNotNull('password_hash')
            ->update(['$rename' => ['password_hash' => 'password']]);

        Schema::connection('mongodb')->table('users', function (Blueprint $collection) {
            $collection->dropColumn('last_login');
        });
    }
};
