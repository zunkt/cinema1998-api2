<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatRoom extends Model
{
    use HasFactory;
    public $table = 'seat_room';

    public $fillable = [
        'value',
        'room_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'value' => 'string',
        'room_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'value' => 'required|string|max:100',
        'room_id' => 'required|integer|max:100',
    ];

    //Relation
    /**
     * @return HasMany
     */
    public function seat()
    {
        return $this->hasMany(\App\Models\Seat::class, 'seat_id');
    }

    /**
     * @return BelongsTo
     */
    public function room()
    {
        return $this->belongsTo(\App\Models\Room::class, 'room_id');
    }
}
