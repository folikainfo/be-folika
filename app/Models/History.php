<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;


class History extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id',
        'users_id',
        'advices_id',
        'image_url',
        'image_public_id',
        'prediction',
        'confidence',
    ];

    // ⬇️ Tambahkan ini:
    protected $appends = ['formatted_date'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advice(): BelongsTo
    {
        return $this->belongsTo(Advice::class, 'advices_id');
    }

    // ⬇️ Accessor untuk formatted date
    public function getFormattedDateAttribute()
    {
        \Carbon\Carbon::setLocale('id');
        return \Carbon\Carbon::parse($this->created_at)->translatedFormat('d F Y');
    }
}
