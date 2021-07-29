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
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'trailer_url' => $this->trailer_url,
            'director' => $this->director,
            'language' => json_decode($this->language),
            'schedule' => $this->schedule,
            'actor' => $this->actor,
            'year' => $this->year,
            'long_time' => $this->long_time,
            'rating' => $this->rating,
            'descriptionContent' => $this->descriptionContent,
            'type' => $this->type,
            'slot' => $this->slot,
            'imageText' => $this->imageText,
            'backgroundImage' => $this->backgroundImage,
            'releaseDate' => $this->releaseDate
        ];
    }
}
