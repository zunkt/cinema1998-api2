<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Movie extends Model
{
    public $table = 'movie';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $appends = ['image_url'];

    public $fillable = [
        'name',
        'image',
        'trailer_url',
        'director',
        'language',
        'actor',
        'year',
        'long_time',
        'rating',
        'descriptionContent',
        'type',
        'slot',
        'imageText',
        'backgroundImage'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'image' => 'string',
        'trailer_url' => 'string',
        'director' => 'string',
        'language' => 'string',
        'actor' => 'string',
        'year' => 'integer',
        'long_time' => 'double',
        'rating' => 'double',
        'descriptionContent' => 'string',
        'type' => 'string',
        'slot' => 'integer',
        'imageText' => 'string',
        'backgroundImage' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:100',
        'image' => 'nullable',
        'trailer_url' => 'nullable|string|max:100',
        'director' => 'nullable|string|max:100',
        'language' => 'nullable|string|max:100',
        'actor' => 'nullable|string|max:100',
        'year' => 'nullable|integer|max:100',
        'long_time' => 'nullable|integer|max:100',
        'rating' => 'nullable|integer|max:100',
        'descriptionContent' => 'nullable|string',
        'type' => 'nullable|string|max:100',
        'slot' => 'nullable|integer|max:100',
        'imageText' => 'nullable|string',
        'backgroundImage' => 'nullable|string|max:100',
    ];
    /**
     * @var mixed
     */

    /**
     * @return hasMany
     **/
    public function feedback()
    {
        return $this->hasMany(\App\Models\FeedBack::class, 'movie_id');
    }

    /**
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        return Storage::exists($this->image) ? Storage::url($this->image) : null;
    }
}
