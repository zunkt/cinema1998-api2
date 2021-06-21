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
        'name',
        'password',
        'full_name',
        'email',
        'phone',
        'is_verified_email',
        'is_phone_verified',
        'is_token_phone',
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
        'phone' => 'string',
        'is_verified_email' => 'string',
        'is_phone_verified' => 'string',
        'is_token_phone' => 'string',
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
        'email' => 'required|email|max:255',
        'name' => 'required|string|max:100',
        'full_name' => 'required|string|max:255',
        'phone' => 'required|string|max:255',
        'is_phone_verified' => 'nullable|boolean',
        'is_verified_email' => 'nullable|boolean',
        'is_token_phone' => 'nullable|boolean',
        'ban_at' => 'nullable',
        'id_social' => 'nullable|boolean',
        'created_at' => 'required',
        'deleted_at' => 'nullable',
    ];

    //Relation

    /**
     * @return HasMany
     **/
    public function userSendDeals()
    {
        return $this->hasMany(\App\Models\Deal::class, 'user_send_id');
    }

    /**
     * @return HasMany
     **/
    public function userReceiveDeals()
    {
        return $this->hasMany(\App\Models\Deal::class, 'user_receive_id');
    }

    /**
     * @return HasMany
     **/
    public function userSendContacts()
    {
        return $this->hasMany(\App\Models\Contact::class, 'user_send_id');
    }

    /**
     * @return HasMany
     **/
    public function userReceiveContacts()
    {
        return $this->hasMany(\App\Models\Contact::class, 'user_friend_id');
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
