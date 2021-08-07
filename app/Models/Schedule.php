<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Schedule extends Model
{
    public $table = 'schedule';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'date_start',
        'time_start',
        'time_end',
        'movie_id',
        'room_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'date_start' => 'string',
        'time_start' => 'datetime',
        'time_end' => 'datetime',
        'movie_id' => 'integer',
        'room_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'date_start' => 'required|string|max:100',
        'created_at' => 'required',
    ];

    //Relation
    /**
     * @return hasMany
     **/
    public function ticket()
    {
        return $this->hasMany(\App\Models\Ticket::class, 'schedule_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room()
    {
        return $this->belongsTo(\App\Models\Room::class, 'room_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function movie()
    {
        return $this->belongsTo(\App\Models\Movie::class, 'movie_id');
    }
}
