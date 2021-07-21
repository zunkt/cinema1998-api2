<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    public $table = 'schedule';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'name',
        'time_start',
        'time_end',
        'movie_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'time_start' => 'datetime',
        'time_end' => 'datetime',
        'movie_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:100',
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
     * @return hasMany
     **/
    public function room()
    {
        return $this->hasMany(\App\Models\Room::class, 'schedule_id');
    }

    /**
     * @return BelongsToMany
     */
    public function movie()
    {
        return $this->belongsToMany(\App\Models\Movie::class, 'movie_id');
    }
}
