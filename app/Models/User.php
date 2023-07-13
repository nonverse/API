<?php

namespace App\Models;

use App\Services\User\OAuth2\HasApiTokens;
use App\Services\User\UsesRecovery;
use App\Services\User\VerifiesEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens, VerifiesEmail, UsesRecovery;

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
        'username',
        'email',
        'phone',
        'dob',
        'gender',
        'password',
        'admin',
        'restrictions',
        'phone_verified_at',
        'email_verified_at',
        'totp_secret',
        'use_totp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pin',
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
