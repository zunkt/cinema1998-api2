<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FeedBack extends Model
{
    public $table = 'feedback';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'content',
        'movie_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'content' => 'string',
        'movie_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'content' => 'required|string|max:100',
        'created_at' => 'required',
    ];

    /**
     * @return BelongsToMany
     */
    public function movie()
    {
        return $this->belongsToMany(\App\Models\Movie::class, 'movie_id');
    }
}
