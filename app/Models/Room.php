<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    public $table = 'room';

    public $fillable = [
        'name',
        'room_number',
        'theater_id',
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
        'room_number' => 'integer',
        'theater_id' => 'integer',
        'schedule_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:100',
        'room_number' => 'required|integer|max:100',
        'theater_id' => 'required|integer|max:100',
        'schedule_id' => 'required|integer|max:100',
    ];

    /**
     * @return hasMany
     **/
    public function room()
    {
        return $this->hasMany(\App\Models\Seat::class, 'room_id');
    }

    /**
     * @return BelongsToMany
     */
    public function schedule()
    {
        return $this->belongsToMany(\App\Models\Schedule::class, 'schedule_id');
    }

    /**
     * @return BelongsToMany
     */
    public function theater()
    {
        return $this->belongsToMany(\App\Models\Theater::class, 'theater_id');
    }
}
