<?php
// ══════════════════════════════════════════════════════════════
// FILE: app/Models/User.php
// ══════════════════════════════════════════════════════════════

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name', 'email', 'password',
        'gender', 'age', 'region', 'education_level',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'age'        => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // JWT Interface
    public function getJWTIdentifier()        { return $this->getKey(); }
    public function getJWTCustomClaims(): array { return []; }

    // Relasi ke questionnaires
    public function questionnaires()
    {
        return $this->hasMany(Questionnaire::class, 'user_id');
    }

    // Relasi ke ml_results (via questionnaire)
    public function mlResults()
    {
        return $this->hasManyThrough(MlResult::class, Questionnaire::class, 'user_id', 'questionnaire_id');
    }
}