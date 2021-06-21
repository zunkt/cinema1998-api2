<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserLoginHistoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($resource)
    {
        return [
            'data' => $this->collection,
            'meta' => (object)[
                'current_page' => $this->currentPage(),
                'total' => $this->total(),
                'last_page' => $this->lastPage()
            ]
        ];
    }
}
