<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ticket extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public const STATUSES = [
        'new' => 'Новая',
        'pending' => 'В ожидании',
        'resolved' => 'Решена',
    ];

    protected $guarded = [];

    protected function text(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['description'] ?? $value,
        );
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
}
