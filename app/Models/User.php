<?php

namespace App\Models;

use App\Notifications\ForgotPasswordUser;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property mixed email
 */
class User extends  Authenticatable implements JWTSubject, CanResetPassword
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable;

    public $table = 'user';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'email',
        'password',
        'full_name',
        'identityNumber',
        'address',
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
        'name' => 'string',
        'password' => 'string',
        'full_name' => 'string',
        'email' => 'string',
        'identityNumber' => 'string',
        'address' => 'string',
        'failed_login_attempts' => 'integer',
        'ban_at' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'password' => 'required|string|max:100',
        'email' => 'required|email:rfc,dns',
        'full_name' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
        'identityNumber' => 'nullable|string|max:255',
        'ban_at' => 'nullable',
        'created_at' => 'required',
        'deleted_at' => 'nullable',
    ];

    //Relation
    /**
     * @return HasMany
     **/
    public function ticket()
    {
        return $this->hasMany(\App\Models\Ticket::class, 'user_id');
    }

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
        $this->notify(new ForgotPasswordUser($token, $this->email));
    }
}
