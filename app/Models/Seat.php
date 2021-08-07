<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Seat extends Model
{
    use HasFactory;
    public $table = 'seat';

    public $fillable = [
        'value',
        'status',
        'price',
        'ticket_id',
        'room_id',
        'schedule_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'value' => 'string',
        'status' => 'string',
        'price' => 'double',
        'ticket_id' => 'integer',
        'room_id' => 'integer',
        'schedule_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'value' => 'required|string|max:100',
        'status' => 'required|string|max:100',
        'price' => 'required|max:100',
        'ticket_id' => 'required|integer|max:100',
        'room_id' => 'required|integer|max:100',
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
    public function room()
    {
        return $this->belongsTo(\App\Models\Room::class, 'room_id');
    }

    /**
     * @return BelongsTo
     */
    public function schedule()
    {
        return $this->belongsTo(\App\Models\Schedule::class, 'schedule_id');
    }
}
