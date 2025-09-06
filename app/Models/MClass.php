<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MClass extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'classes';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name'
    ];
}
