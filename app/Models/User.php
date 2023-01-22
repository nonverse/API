<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens ,HasFactory, Notifiable, SoftDeletes;

    /**
     * The primary key associated with the table
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Indicates if the model's primary key is auto-incrementing
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Data type of the model's primary key
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_first',
        'name_last',
        'email',
        'phone',
        'dob',
        'password',
        'admin',
        'restrictions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'totp_secret',
        'totp_recovery_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
