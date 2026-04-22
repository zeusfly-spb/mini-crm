<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
