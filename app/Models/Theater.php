<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theater extends Model
{
    public $table = 'theater';

    public $fillable = [
        'name',
        'address',
        'phone',
        'direction',
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
        'direction' => 'string',
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
        'direction' => 'nullable|string|max:100',
        'created_at' => 'required',
    ];

    /**
     * @return hasMany
     **/
    public function room()
    {
        return $this->hasMany(\App\Models\Room::class, 'theater_id');
    }
}
