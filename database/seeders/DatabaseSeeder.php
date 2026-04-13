<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\MlResult;
use App\Models\Questionnaire;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * FILE: database/seeders/DatabaseSeeder.php
 *
 * Jalankan: php artisan db:seed
 * Reset + seed ulang: php artisan migrate:fresh --seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Admin default ──────────────────────────────────
        AdminUser::updateOrCreate(
            ['email' => 'admin@digitalliving.app'],
            [
                'name'     => 'Super Admin',
                'email'    => 'admin@digitalliving.app',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        $this->command->info('Admin user created: admin@digitalliving.app / admin123');

        // ── 2. Demo user ──────────────────────────────────────
        $demo = User::updateOrCreate(
            ['email' => 'demo@digitalliving.app'],
            [
                'name'            => 'Demo User',
                'email'           => 'demo@digitalliving.app',
                'password'        => Hash::make('demo1234'),
                'gender'          => 'male',
                'age'             => 21,
                'region'          => 'Jawa Timur',
                'education_level' => 'S1',
            ]
        );

        $this->command->info('Demo user created: demo@digitalliving.app / demo1234');

        // ── 3. Dummy surveys + ML results (7 hari) ──────────────
        $focusValues = [55, 60, 58, 72, 68, 75, 70];

        foreach ($focusValues as $i => $focus) {
            $q = Questionnaire::create([
                'user_id'                => $demo->_id,
                'income_level'           => 'menengah',
                'daily_role'             => 'mahasiswa',
                'device_hours_per_day'   => 8.5 - ($i * 0.2),
                'phone_unlocks_per_day'  => 70 - ($i * 2),
                'notifications_per_day'  => 120,
                'social_media_minutes'   => 200 - ($i * 10),
                'study_minutes'          => 90 + ($i * 15),
                'physical_activity_days' => 2 + ($i % 3),
                'sleep_hours'            => 6.5,
                'sleep_quality'          => 6,
                'anxiety_score'          => 5,
                'depression_score'       => 4,
                'stress_level'           => 5,
                'happiness_score'        => 6 + ($i * 0.2),
                'created_at'             => now()->subDays(6 - $i),
                'updated_at'             => now()->subDays(6 - $i),
            ]);

            $result = MlResult::create([
                'user_id'                   => $demo->_id,
                'questionnaire_id'          => $q->_id,
                'focus_score'               => $focus,
                'productivity_score'        => $focus - 5 + rand(-3, 3),
                'digital_dependence_score'  => 80 - $focus,
                'high_risk_flag'            => $focus < 60,
                'created_at'               => now()->subDays(6 - $i),
                'updated_at'               => now()->subDays(6 - $i),
            ]);

            Recommendation::create([
                'result_id'       => $result->_id,
                'recommendations' => [
                    'Kurangi penggunaan media sosial',
                    'Tidur minimal 7 jam per hari',
                    'Olahraga minimal 3x seminggu',
                ],
                'created_at'      => now()->subDays(6 - $i),
            ]);
        }

        $this->command->info('7 dummy surveys + ML results seeded untuk demo user');
    }
}