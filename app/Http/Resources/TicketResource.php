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
            'schedule_id' => $this->schedule_id,
            'user_id' => $this->user_id,
            'bill_id' => $this->bill_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
