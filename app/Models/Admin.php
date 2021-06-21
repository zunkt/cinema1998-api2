<?php

namespace App\Models;

use App\Notifications\ForgotPassword;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class Admin
 * @package App\Models
 * @version May 25, 2021, 03:27 pm UTC
 * @property mixed email
 */
class Admin extends Authenticatable implements JWTSubject, CanResetPassword
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable;

    public $table = 'admin';

    const CREATED_AT = 'created_at';

    protected $dates = ['deleted_at'];

    protected $hidden = ['password'];

    public $fillable = [
        'name',
        'password',
        'full_name',
        'email',
        'failed_login_attempts',
        'ban_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'email' => 'string',
        'name' => 'string',
        'full_name' => 'string',
        'password' => 'string',
        'ban_at' => 'datetime',
        'failed_login_attempts' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'password' => 'required|string|max:100',
        'email' => 'required|email|max:255',
        'name' => 'required|string|max:100',
        'full_name' => 'required|string|max:255',
        'created_at' => 'required',
        'deleted_at' => 'nullable',
        'ban_at' => 'nullable',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ForgotPassword($token, $this->email));
    }
}
