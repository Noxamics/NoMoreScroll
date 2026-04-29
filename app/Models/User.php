<?php
// ══════════════════════════════════════════════════════════════
// FILE: app/Models/User.php
// ══════════════════════════════════════════════════════════════

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name', 'email', 'password',
        'gender', 'date_of_birth', 'region', 'education_level',
        'daily_role', 'income_level',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'last_login'    => 'datetime',
    ];

    protected $appends = ['age'];

    // JWT Interface
    public function getJWTIdentifier()        { return $this->getKey(); }
    public function getJWTCustomClaims(): array { return []; }

    /**
     * Hitung umur otomatis dari date_of_birth
     */
    public function getAgeAttribute(): ?int
    {
        if (! $this->date_of_birth) return null;
        return Carbon::parse($this->date_of_birth)->age;
    }

    // Relasi ke questionnaires
    public function questionnaires()
    {
        return $this->hasMany(Questionnaire::class, 'user_id');
    }

    // Relasi ke ml_results
    public function mlResults()
    {
        return $this->hasMany(MlResult::class, 'user_id');
    }
}