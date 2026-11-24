<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Http\Requests\StoreRequestRequest;
use App\Models\InternalRequest;
use App\Models\User;
use App\Notifications\RequestReplied;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequestController extends Controller
{
    use AuthorizesRequests;

    /**
     * Liste des demandes reçues et envoyées.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', InternalRequest::class);

        $tab = $request->input('tab') === 'sent' ? 'sent' : 'received';

        $query = InternalRequest::with(['sender', 'recipient'])
            ->withCount('replies')
            ->latest('created_at');

        if ($tab === 'sent') {
            $query->where('sender_id', auth()->id());
        } else {
            $query->where('recipient_id', auth()->id());
        }

        $status = $request->input('status');

        if (is_string($status) && RequestStatus::tryFrom($status) !== null) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(10)->withQueryString();

        $pendingCount = InternalRequest::where('recipient_id', auth()->id())
            ->where('status', RequestStatus::OPEN)
            ->count();

        return view('requests.index', [
            'requests' => $requests,
            'tab' => $tab,
            'statuses' => RequestStatus::cases(),
            'status' => $status,
            'pendingCount' => $pendingCount,
        ]);
    }

    /**
     * Formulaire de création d'une demande.
     */
    public function create(): View
    {
        $this->authorize('create', InternalRequest::class);

        return view('requests.create', [
            'recipients' => $this->recipientOptions(),
        ]);
    }

    /**
     * Enregistrer une nouvelle demande.
     */
    public function store(StoreRequestRequest $request): RedirectResponse
    {
        $this->authorize('create', InternalRequest::class);

        $validated = $request->validated();

        $internalRequest = InternalRequest::create([
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'recipient_id' => $validated['recipient_id'],
            'sender_id' => auth()->id(),
            'status' => RequestStatus::OPEN,
        ]);

        return redirect()
            ->route('requests.show', $internalRequest)
            ->with('success', 'Demande envoyée avec succès.');
    }

    /**
     * Consulter une demande et son fil de réponses.
     */
    public function show(InternalRequest $internalRequest): View
    {
        $this->authorize('view', $internalRequest);

        $internalRequest->load(['sender', 'recipient', 'replies.user']);

        return view('requests.show', compact('internalRequest'));
    }

    /**
     * Ajouter une réponse à une demande.
     */
    public function reply(Request $request, InternalRequest $internalRequest): RedirectResponse
    {
        $this->authorize('reply', $internalRequest);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ], [
            'body.required' => 'Le message de la réponse est obligatoire.',
            'body.string' => 'Le message de la réponse doit être un texte.',
            'body.max' => 'Le message de la réponse ne doit pas dépasser 5000 caractères.',
        ], [
            'body' => 'réponse',
        ]);

        $reply = $internalRequest->replies()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        if (auth()->id() === $internalRequest->recipient_id) {
            $internalRequest->update(['status' => RequestStatus::ANSWERED]);
        }

        $otherParticipantId = auth()->id() === $internalRequest->sender_id
            ? $internalRequest->recipient_id
            : $internalRequest->sender_id;

        $otherParticipant = User::find($otherParticipantId);

        if ($otherParticipant !== null) {
            $otherParticipant->notify(new RequestReplied($internalRequest, auth()->user(), $reply));
        }

        return back()->with('success', 'Réponse ajoutée avec succès.');
    }

    /**
     * Clôturer une demande.
     */
    public function close(InternalRequest $internalRequest): RedirectResponse
    {
        $this->authorize('close', $internalRequest);

        $internalRequest->update(['status' => RequestStatus::CLOSED]);

        return back()->with('success', 'Demande clôturée avec succès.');
    }

    /** @return array<int, string> */
    private function recipientOptions(): array
    {
        $roles = auth()->user()->isEmployee()
            ? ['manager', 'admin']
            : ['admin'];

        return User::where('is_active', true)
            ->whereIn('role', $roles)
            ->whereKeyNot(auth()->id())
            ->orderBy('name')
            ->get(['id', 'name', 'role'])
            ->mapWithKeys(fn (User $user): array => [
                $user->id => $user->name.' — '.($user->isAdmin() ? 'Administrateur' : 'Manager'),
            ])
            ->all();
    }
}
