<?php

namespace App\Models;

use Database\Factories\InternalRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InternalRequest extends Model
{
    /** @use HasFactory<InternalRequestFactory> */
    use HasFactory;

    protected $fillable = ['sender_id', 'recipient_id', 'subject', 'message', 'status'];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(RequestReply::class);
    }
}
