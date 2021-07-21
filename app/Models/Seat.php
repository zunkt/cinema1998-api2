<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Seat extends Model
{
    public $table = 'seat';

    public $fillable = [
        'seat_number',
        'ticket_id',
        'room_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'seat_number' => 'integer',
        'ticket_id' => 'integer',
        'room_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:100',
        'seat_number' => 'require|integer|max:100',
        'ticket_id' => 'require|integer|max:100',
        'room_id' => 'require|integer|max:100',
    ];

    //Relation
    /**
     * @return BelongsTo
     **/
    public function ticket()
    {
        return $this->belongsTo(\App\Models\Ticket::class, 'ticket_id');
    }

    /**
     * @return BelongsToMany
     */
    public function room()
    {
        return $this->belongsToMany(\App\Models\Room::class, 'room_id');
    }
}
