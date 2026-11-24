<?php

namespace App\Notifications;

use App\Models\InternalRequest;
use App\Models\RequestReply;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestReplied extends Notification
{
    use Queueable;

    public function __construct(
        public InternalRequest $internalRequest,
        public User $author,
        public RequestReply $reply,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nouvelle réponse à une demande',
            'message' => InternalRequest::participantName($this->author).' a répondu à la demande « '.$this->internalRequest->subject.' ».',
            'request_title' => $this->internalRequest->subject,
            'author_name' => InternalRequest::participantName($this->author),
            'reply' => $this->reply->body,
            'url' => route('requests.show', $this->internalRequest),
        ];
    }
}
