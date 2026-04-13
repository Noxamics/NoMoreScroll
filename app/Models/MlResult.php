<?php
// ══════════════════════════════════════════════════════════════
// FILE: app/Models/MlResult.php
// ══════════════════════════════════════════════════════════════

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class MlResult extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'ml_results';

    protected $fillable = [
        'user_id', 'questionnaire_id',
        'focus_score', 'productivity_score', 'digital_dependence_score',
        'high_risk_flag',
    ];

    protected $casts = [
        'focus_score'               => 'float',
        'productivity_score'        => 'float',
        'digital_dependence_score'  => 'float',
        'high_risk_flag'            => 'boolean',
    ];

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class, 'questionnaire_id');
    }

    public function recommendations()
    {
        return $this->hasOne(Recommendation::class, 'result_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
