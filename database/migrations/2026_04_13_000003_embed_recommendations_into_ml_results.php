<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * FILE: database/migrations/2026_04_13_000003_embed_recommendations_into_ml_results.php
 *
 * Perubahan pada collection 'ml_results':
 *   - Embed array 'recommendations' langsung di dokumen (dari collection terpisah)
 *   - Tambah field 'updated_at'
 *   - Field 'created_at' sudah ada, dipertahankan
 *
 * Struktur akhir:
 *   _id, user_id, questionnaire_id,
 *   focus_score, productivity_score, digital_dependence_score,
 *   high_risk_flag,
 *   recommendations: [ "...", "..." ],
 *   created_at, updated_at
 *
 * ⚠️  Jalankan migration ini SEBELUM drop collection recommendations (migration 000004)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Ambil semua dokumen dari collection recommendations
        $allRecs = DB::connection('mongodb')
            ->collection('recommendations')
            ->get();

        // Kelompokkan per result_id
        $grouped = [];
        foreach ($allRecs as $rec) {
            $resultId          = (string) ($rec['result_id'] ?? '');
            $grouped[$resultId] = $rec['recommendations'] ?? [];
        }

        // Embed ke masing-masing dokumen ml_results
        foreach ($grouped as $resultId => $recommendations) {
            DB::connection('mongodb')
                ->collection('ml_results')
                ->where('_id', $resultId)
                ->update([
                    'recommendations' => $recommendations,
                    'updated_at'       => now(),
                ]);
        }

        // Dokumen ml_results yang tidak punya pasangan di recommendations
        // → set recommendations = [] dan updated_at
        DB::connection('mongodb')
            ->collection('ml_results')
            ->whereNull('recommendations')
            ->update([
                '$set' => [
                    'recommendations' => [],
                    'updated_at'       => now(),
                ],
            ]);
    }

    public function down(): void
    {
        // Kembalikan data recommendations ke collection terpisah
        $mlResults = DB::connection('mongodb')
            ->collection('ml_results')
            ->whereNotNull('recommendations')
            ->get();

        foreach ($mlResults as $result) {
            if (!empty($result['recommendations'])) {
                DB::connection('mongodb')
                    ->collection('recommendations')
                    ->insert([
                        'result_id'       => $result['_id'],
                        'recommendations' => $result['recommendations'],
                        'created_at'      => $result['created_at'] ?? now(),
                        'updated_at'      => $result['updated_at'] ?? now(),
                    ]);
            }
        }

        // Hapus field recommendations dan updated_at dari ml_results
        DB::connection('mongodb')
            ->collection('ml_results')
            ->update([
                '$unset' => [
                    'recommendations' => '',
                    'updated_at'       => '',
                ],
            ]);
    }
};
