<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class MlResult extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'ml_results';

    protected $fillable = [
        'user_id',
        'questionnaire_id',
        'digital_dependence_score',
        'recommendations',        // array string, embedded
    ];

    protected $casts = [
        'digital_dependence_score' => 'float',
        'recommendations' => 'array',
    ];

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class, 'questionnaire_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}