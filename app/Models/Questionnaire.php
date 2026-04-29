<?php
// ══════════════════════════════════════════════════════════════
// FILE: app/Models/Questionnaire.php
// ══════════════════════════════════════════════════════════════

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Questionnaire extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'questionnaires';

    protected $fillable = [
        'user_id',
        'device_type',
        'device_hours_per_day', 'phone_unlocks_per_day', 'notifications_per_day',
        'social_media_minutes', 'study_minutes', 'physical_activity_days',
        'sleep_hours', 'sleep_quality',
        'anxiety_score', 'depression_score', 'stress_level', 'happiness_score',
    ];

    protected $casts = [
        'device_hours_per_day'   => 'float',
        'phone_unlocks_per_day'  => 'integer',
        'notifications_per_day'  => 'integer',
        'social_media_minutes'   => 'integer',
        'study_minutes'          => 'integer',
        'physical_activity_days' => 'integer',
        'sleep_hours'            => 'float',
        'sleep_quality'          => 'float',
        'anxiety_score'          => 'float',
        'depression_score'       => 'float',
        'stress_level'           => 'float',
        'happiness_score'        => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mlResult()
    {
        return $this->hasOne(MlResult::class, 'questionnaire_id');
    }
}