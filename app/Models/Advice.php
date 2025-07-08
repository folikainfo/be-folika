<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Advice extends Model
{

    protected $table      = 'advices';
    protected $primaryKey = 'id';
    protected $fillable = [
        'prediction',
        'result',
        'description',
        'advice',
        'route',
    ];


    protected $casts = [
        'advice' => 'array',
    ];

    public function histories(): HasMany
    {
        return $this->hasMany(History::class);
    }
}
