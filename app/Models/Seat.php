<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seat extends Model
{
    use HasFactory;
    public $table = 'seat';

    public $fillable = [
        'status',
        'price',
        'ticket_id',
        'seat_id',
        'schedule_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => 'string',
        'price' => 'double',
        'ticket_id' => 'integer',
        'seat_id' => 'integer',
        'schedule_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'status' => 'required|string|max:100',
        'price' => 'required|max:100',
        'ticket_id' => 'required|integer|max:100',
        'seat_id' => 'required|integer|max:100',
        'schedule_id' => 'required|integer|max:100',
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
     * @return BelongsTo
     */
    public function seat_room()
    {
        return $this->belongsTo(\App\Models\SeatRoom::class, 'seat_id');
    }

    /**
     * @return BelongsTo
     */
    public function schedule()
    {
        return $this->belongsTo(\App\Models\Schedule::class, 'schedule_id');
    }
}
