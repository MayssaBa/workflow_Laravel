<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestReply extends Model
{
    /** @use HasFactory<RequestReplyFactory> */
    use HasFactory;

    protected $fillable = [
        'body',
        'internal_request_id',
        'user_id',
    ];

    public function internalRequest(): BelongsTo
    {
        return $this->belongsTo(InternalRequest::class, 'internal_request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
?>

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestReply extends Model
{
    /** @use HasFactory<\Database\Factories\RequestReplyFactory> */
    use HasFactory;
}
