<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    use HasFactory;

    public $table = 'room';

    public $fillable = [
        'name',
        'room_number',
        'theater_id',
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
    ];

    /**
     * @return hasMany
     **/
    public function seat_room()
    {
        return $this->hasMany(\App\Models\SeatRoom::class, 'room_id');
    }

    /**
     * @return HasMany
     */
    public function schedule()
    {
        return $this->hasMany(\App\Models\Schedule::class, 'room_id');
    }

    /**
     * @return BelongsTo
     */
    public function theater()
    {
        return $this->belongsTo(\App\Models\Theater::class, 'theater_id');
    }
}
