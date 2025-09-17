<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasUuids, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'student_id',
        'year',
        'date',
        'price',
        'type', // Manual, Payment Gateway
        'status', // Not Yet, Waiting for Validation, OK
        'snap_token',
        'expired_at',
    ];

    // cast expired_at ke datetime otomatis
    protected $casts = [
        'expired_at' => 'datetime',
        'date'       => 'date',
    ];

    // Transaction.php
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }
}