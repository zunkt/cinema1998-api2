<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'status' => $this->status,
            'price' => $this->price,
            'ticket' => $this->ticket,
            'schedule' => $this->schedule,
            'room' => $this->room,
        ];
    }
}
