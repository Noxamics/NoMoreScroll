<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * FILE: database/migrations/2026_04_13_000006_migrate_recommendation_rules_to_mongodb.php
 *
 * Pindahkan recommendation_rules dari MySQL → MongoDB dengan struktur baru.
 *
 * Struktur baru (MongoDB):
 *   _id, rule_id, condition_field, condition_operator, condition_value,
 *   recommendation_text, category, priority (integer, 1=tertinggi),
 *   is_active, created_by (ObjectId string), created_at, updated_at
 *
 * Perbedaan dari struktur MySQL lama:
 *   - 'name'           → dihapus (tidak ada di struktur baru)
 *   - 'variable'       → rename ke 'condition_field'
 *   - 'operator'       → rename ke 'condition_operator'
 *   - 'value'          → rename ke 'condition_value'
 *   - 'recommendation' → rename ke 'recommendation_text'
 *   - 'priority'       → dari enum (high/medium/low) → integer (1/2/3)
 *   - Tambah: rule_id, category, created_by
 *
 * Langkah setelah migration ini berhasil:
 *   → Jalankan migration 000007 untuk drop tabel MySQL
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Buat collection baru di MongoDB ──────────────────────────────────
        Schema::connection('mongodb')->create('recommendation_rules', function (Blueprint $collection) {
            $collection->id();
            $collection->string('rule_id')->unique();       // "rule_001", "rule_002", dst.
            $collection->string('condition_field');         // field yang dicek, misal "sleep_hours"
            $collection->string('condition_operator');      // "<", ">", "<=", ">=", "=="
            $collection->float('condition_value');          // nilai threshold
            $collection->string('recommendation_text');     // teks rekomendasi ke user
            $collection->string('category')->nullable();    // "sleep", "social_media", "exercise", dst.
            $collection->integer('priority')->default(1);   // 1 = paling penting
            $collection->boolean('is_active')->default(true);
            $collection->string('created_by')->nullable();  // ObjectId admin (string)
            $collection->timestamps();

            // Index untuk performa query di engine rekomendasi
            $collection->index('condition_field');
            $collection->index('is_active');
            $collection->index('priority');
        });

        // ── Migrasi data dari MySQL → MongoDB ────────────────────────────────
        try {
            $mysqlRules = DB::table('recommendation_rules')->get();

            foreach ($mysqlRules as $index => $rule) {
                DB::connection('mongodb')
                    ->collection('recommendation_rules')
                    ->insert([
                        'rule_id'             => 'rule_' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                        'condition_field'     => $rule->variable      ?? '',
                        'condition_operator'  => $rule->operator      ?? '<',
                        'condition_value'     => (float) ($rule->value ?? 0),
                        'recommendation_text' => $rule->recommendation ?? '',
                        'category'            => null, // isi manual atau via seeder
                        'priority'            => match ($rule->priority ?? 'low') {
                            'high'   => 1,
                            'medium' => 2,
                            default  => 3,
                        },
                        'is_active'  => (bool) ($rule->is_active ?? true),
                        'created_by' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
        } catch (\Exception $e) {
            // Tabel MySQL sudah tidak ada atau kosong — lanjut, isi via seeder
        }
    }

    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('recommendation_rules');
    }
};
