<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @property string $uuid
 * @property string $mc_uuid
 * @property string $mc_username
 * @property integer $rank
 * @property string $group
 * @property object $teams
 * @property Carbon $profile_verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */

class Profile extends Model
{
    use HasFactory;

    protected $connection = 'minecraft';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';
}
