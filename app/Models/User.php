<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'class_id',
        'academic_year_id',
        'nisn',
        'first_name',
        'last_name',
        'gender',
        'parent_status',
        'parent_name',
        'phone',
        'address',
        'email',
        'password',
        'picture_path'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke transaksi (User memiliki banyak Transaction)
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'student_id', 'id');
        // 'student_id' = foreign key di tabel transactions
        // 'id' = primary key di tabel users
    }

    // Relasi role jika one-to-many
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function mclass() // gunakan nama lain, bukan 'class'
    {
        return $this->belongsTo(MClass::class, 'class_id', 'id');
    }
}
