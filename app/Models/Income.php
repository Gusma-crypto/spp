<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{ 
    use HasUuids, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'date',
        'total_income',
        'note'
    ];
}
 