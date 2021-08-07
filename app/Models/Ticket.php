<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    public $table = 'ticket';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'name',
        'user_id',
        'schedule_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'user_id' => 'integer',
        'schedule_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:100',
        'user_id' => 'integer',
        'schedule_id' => 'integer',
        'created_at' => 'required',
    ];

    //Relation
    /**
     * @return BelongsTo
     **/
    public function schedule()
    {
        return $this->belongsTo(\App\Models\Schedule::class, 'schedule_id');
    }

    /**
     * @return BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * @return HasOne
     **/
    public function bill()
    {
        return $this->hasOne(\App\Models\Bill::class, 'ticket_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function seat()
    {
        return $this->hasMany(\App\Models\Seat::class, 'ticket_id');
    }

//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\HasMany
//     */
//    public function corn()
//    {
//        return $this->hasMany(\App\Models\Seat::class, 'ticket_id');
//    }
}
