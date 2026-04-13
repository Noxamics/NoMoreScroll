<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Collection: recommendation_rules
 *
 * Fields:
 *   _id, rule_id, condition_field, condition_operator, condition_value,
 *   recommendation_text, category, priority (int: 1=tinggi),
 *   is_active, created_by, created_at, updated_at
 *
 * Catatan: Langsung MongoDB — tidak perlu tabel MySQL sama sekali.
 *          Isi data via RecommendationRulesSeeder.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mongodb')->create('recommendation_rules', function (Blueprint $collection) {
            $collection->string('rule_id')->unique();
            $collection->string('condition_field')->index();
            $collection->string('condition_operator');
            $collection->float('condition_value');
            $collection->string('recommendation_text');
            $collection->string('category')->nullable();
            $collection->integer('priority')->default(1)->index();
            $collection->boolean('is_active')->default(true)->index();
            $collection->string('created_by')->nullable(); // ObjectId admin sebagai string
            $collection->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('recommendation_rules');
    }
};
