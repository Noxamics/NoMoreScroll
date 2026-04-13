<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AdminOtp extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'admin_otps';

    protected $fillable = [
        'admin_id',
        'email',
        'otp_code',
        'attempts',
        'expired_at',
        'verified',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'verified'   => 'boolean',
        'attempts'   => 'integer',
    ];

    /**
     * Generate OTP code 6 digit
     */
    public static function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check apakah OTP masih valid (belum expired)
     */
    public function isExpired(): bool
    {
        return now()->isAfter($this->expired_at);
    }

    /**
     * Check apakah attempts sudah mencapai limit
     */
    public function isLimitExceeded(): bool
    {
        return $this->attempts >= 5;
    }

    /**
     * Relasi ke AdminUser
     */
    public function admin()
    {
        return $this->belongsTo(AdminUser::class, 'admin_id');
    }
}
