<?php

namespace App\Models\OAuth2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    public $table = 'oauth_access_tokens';

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
}
