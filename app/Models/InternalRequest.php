<?php

namespace App\Models;

use App\Enums\RequestStatus;
use App\Policies\RequestPolicy;
use Database\Factories\InternalRequestFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(RequestPolicy::class)]
class InternalRequest extends Model
{
    /** @use HasFactory<InternalRequestFactory> */
    use HasFactory;

    protected $fillable = ['sender_id', 'recipient_id', 'subject', 'message', 'status'];

    protected function casts(): array
    {
        return [
            'status' => RequestStatus::class,
        ];
    }

    public static function participantName(?User $user): string
    {
        if ($user === null) {
            return 'Utilisateur supprimé';
        }

        return $user->name.($user->isActive() ? '' : ' (désactivé)');
    }

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
        return $this->hasMany(RequestReply::class)->oldest();
    }
}
