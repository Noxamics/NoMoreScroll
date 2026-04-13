<?php
// ══════════════════════════════════════════════════════════════
// FILE: app/Models/Recommendation.php
// ══════════════════════════════════════════════════════════════

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Recommendation extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'recommendations';

    protected $fillable = ['result_id', 'recommendations'];

    protected $casts = [
        'recommendations' => 'array',
    ];

    public function mlResult()
    {
        return $this->belongsTo(MlResult::class, 'result_id');
    }
}