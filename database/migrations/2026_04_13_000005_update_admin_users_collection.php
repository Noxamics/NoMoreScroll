<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * FILE: database/migrations/2026_04_13_000005_update_admin_users_collection.php
 *
 * Perubahan pada collection 'admin_users':
 *   - Hapus field 'password'    (auth admin pakai ENV saja)
 *   - Hapus field 'updated_at'  (tidak dibutuhkan)
 *   - Tambah field 'is_active'  (boolean, default true)
 *
 * Struktur akhir:
 *   _id, name, email, role, is_active, created_at
 */
return new class extends Migration
{
    public function up(): void
    {
        // Hapus field password dan updated_at dari semua dokumen
        DB::connection('mongodb')
            ->collection('admin_users')
            ->update([
                '$unset' => [
                    'password'   => '',
                    'updated_at' => '',
                ],
            ]);

        // Tambah is_active = true ke semua dokumen yang belum punya field ini
        DB::connection('mongodb')
            ->collection('admin_users')
            ->whereNull('is_active')
            ->update([
                '$set' => ['is_active' => true],
            ]);
    }

    public function down(): void
    {
        // Kembalikan updated_at
        DB::connection('mongodb')
            ->collection('admin_users')
            ->update([
                '$set' => ['updated_at' => now()],
            ]);

        // Hapus is_active
        DB::connection('mongodb')
            ->collection('admin_users')
            ->update([
                '$unset' => ['is_active' => ''],
            ]);

        // Catatan: field 'password' tidak bisa dikembalikan otomatis
        // karena data sensitif tidak disimpan di migration
    }
};
