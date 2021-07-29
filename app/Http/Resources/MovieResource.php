<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
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
            'name' => $this->name,
            'image' => $this->image,
            'trailer_url' => $this->trailer_url,
            'director' => $this->director,
            'lang' => json_decode($this->language),
            'actor' => $this->actor
        ];
    }
}
