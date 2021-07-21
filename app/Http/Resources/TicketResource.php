<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'name' => $this->name,
            'schedule' => $this->schedule,
            'user' => $this->user,
            'bill' => $this->bill,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
