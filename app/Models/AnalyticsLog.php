<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AnalyticsLog extends Model
{
    /**
     * Nama collection di MongoDB
     */
    protected $collection = 'analytics_logs';

    /**
     * Primary key MongoDB
     */
    protected $primaryKey = '_id';

    /**
     * Laravel timestamps
     */
    public $timestamps = false;

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'user_id',
        'avg_focus_7_days',
        'focus_change_percentage',
        'avg_productivity_7_days',
        'created_at',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'avg_focus_7_days'        => 'float',
        'focus_change_percentage' => 'float',
        'avg_productivity_7_days' => 'float',
        'created_at'              => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}