<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theater extends Model
{
    public $table = 'theater';

    public $fillable = [
        'name',
        'address',
        'phone',
        'movie_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'address' => 'string',
        'phone' => 'string',
        'movie_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:100',
        'address' => 'nullable|string|max:100',
        'phone' => 'nullable|string|max:100',
        'movie_id' => 'required|integer',
        'created_at' => 'required',
    ];
}