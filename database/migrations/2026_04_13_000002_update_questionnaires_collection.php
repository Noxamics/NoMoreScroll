<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * FILE: database/migrations/2026_04_13_000002_update_questionnaires_collection.php
 *
 * Perubahan pada collection 'questionnaires':
 *   - Hapus field 'updated_at' (sesuai struktur target — hanya ada created_at)
 *   - Dokumen yang dibuat ke depan hanya pakai created_at
 *
 * Struktur akhir:
 *   _id, user_id, income_level, daily_role,
 *   device_hours_per_day, phone_unlocks_per_day, notifications_per_day,
 *   social_media_minutes, study_minutes, physical_activity_days,
 *   sleep_hours, sleep_quality,
 *   anxiety_score, depression_score, stress_level, happiness_score,
 *   created_at
 */
return new class extends Migration
{
    public function up(): void
    {
        // Hapus field 'updated_at' dari semua dokumen questionnaires yang ada
        DB::connection('mongodb')
            ->collection('questionnaires')
            ->whereNotNull('updated_at')
            ->update(['$unset' => ['updated_at' => '']]);
    }

    public function down(): void
    {
        // Kembalikan updated_at dengan nilai sama seperti created_at
        $questionnaires = DB::connection('mongodb')
            ->collection('questionnaires')
            ->get();

        foreach ($questionnaires as $q) {
            DB::connection('mongodb')
                ->collection('questionnaires')
                ->where('_id', $q['_id'])
                ->update(['updated_at' => $q['created_at'] ?? now()]);
        }
    }
};
