<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    public $table = 'movie';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

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
    ];
}
