<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
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
            'date_start' => $this->date_start,
            'time_start' => $this->time_start,
            'time_end' => $this->time_end,
            'movie' => $this->movie,
            'room' => $this->room,
        ];
    }
}
