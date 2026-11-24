<?php

namespace Tests\Feature;

use App\Enums\RequestStatus;
use App\Models\InternalRequest;
use App\Models\User;
use App\Notifications\RequestReplied;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RequestTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string, string}> */
    public static function validRecipients(): array
    {
        return [
            'employee to manager' => ['employee', 'manager'],
            'employee to admin' => ['employee', 'admin'],
            'manager to admin' => ['manager', 'admin'],
        ];
    }

    #[DataProvider('validRecipients')]
    public function test_allowed_sender_can_send_a_request(string $senderRole, string $recipientRole): void
    {
        $sender = User::factory()->create(['role' => $senderRole, 'is_active' => true]);
        $recipient = User::factory()->create(['role' => $recipientRole, 'is_active' => true]);

        $this->actingAs($sender)
            ->post(route('requests.store'), $this->payload($recipient->id))
            ->assertSessionHasNoErrors()
            ->assertRedirectContains('/requests/');

        $this->assertDatabaseHas('internal_requests', [
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => RequestStatus::OPEN->value,
        ]);
    }

    public function test_admin_cannot_send_a_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);

        $this->actingAs($admin)
            ->post(route('requests.store'), $this->payload($manager->id))
            ->assertForbidden();

        $this->assertDatabaseCount('internal_requests', 0);
    }

    public function test_employee_cannot_send_a_request_to_another_employee(): void
    {
        $sender = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $recipient = User::factory()->create(['role' => 'employee', 'is_active' => true]);

        $this->actingAs($sender)
            ->post(route('requests.store'), $this->payload($recipient->id))
            ->assertSessionHasErrors(['recipient_id' => 'Le destinataire sélectionné est invalide.']);

        $this->assertDatabaseCount('internal_requests', 0);
    }

    public function test_manager_cannot_send_a_request_to_a_manager(): void
    {
        $sender = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $recipient = User::factory()->create(['role' => 'manager', 'is_active' => true]);

        $this->actingAs($sender)
            ->post(route('requests.store'), $this->payload($recipient->id))
            ->assertSessionHasErrors(['recipient_id' => 'Le destinataire sélectionné est invalide.']);

        $this->assertDatabaseCount('internal_requests', 0);
    }

    public function test_inactive_and_missing_recipients_are_rejected(): void
    {
        $sender = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $inactiveManager = User::factory()->create(['role' => 'manager', 'is_active' => false]);

        $this->actingAs($sender)
            ->post(route('requests.store'), $this->payload($inactiveManager->id))
            ->assertSessionHasErrors(['recipient_id' => 'Le destinataire sélectionné est invalide.']);

        $this->post(route('requests.store'), $this->payload(999999))
            ->assertSessionHasErrors(['recipient_id' => 'Le destinataire sélectionné est invalide.']);

        $this->assertDatabaseCount('internal_requests', 0);
    }

    public function test_sender_cannot_send_a_request_to_themselves(): void
    {
        $sender = User::factory()->create(['role' => 'employee', 'is_active' => true]);

        $this->actingAs($sender)
            ->post(route('requests.store'), $this->payload($sender->id))
            ->assertSessionHasErrors(['recipient_id' => 'Le destinataire sélectionné est invalide.']);

        $this->assertDatabaseCount('internal_requests', 0);
    }

    public function test_third_party_cannot_view_reply_or_close_a_request(): void
    {
        $sender = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $recipient = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $outsider = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $internalRequest = $this->requestBetween($sender, $recipient);

        $this->actingAs($outsider)
            ->get(route('requests.show', $internalRequest))
            ->assertForbidden();

        $this->post(route('requests.reply', $internalRequest), ['body' => 'Réponse interdite'])
            ->assertForbidden();

        $this->patch(route('requests.close', $internalRequest))
            ->assertForbidden();

        $this->assertDatabaseCount('request_replies', 0);
        $this->assertSame(RequestStatus::OPEN, $internalRequest->fresh()->status);
    }

    public function test_recipient_reply_sets_answered_status_and_notifies_only_the_sender(): void
    {
        $sender = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $recipient = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $internalRequest = $this->requestBetween($sender, $recipient);

        $this->actingAs($recipient)
            ->post(route('requests.reply', $internalRequest), ['body' => 'Votre demande a été traitée.'])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame(RequestStatus::ANSWERED, $internalRequest->fresh()->status);
        $this->assertDatabaseHas('request_replies', [
            'internal_request_id' => $internalRequest->id,
            'user_id' => $recipient->id,
            'body' => 'Votre demande a été traitée.',
        ]);
        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $sender->id,
            'type' => RequestReplied::class,
        ]);
    }

    public function test_sender_can_reply_only_after_the_recipient_has_replied(): void
    {
        $sender = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $recipient = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $internalRequest = $this->requestBetween($sender, $recipient);

        $this->actingAs($sender)
            ->post(route('requests.reply', $internalRequest), ['body' => 'Première réponse'])
            ->assertForbidden();

        $this->actingAs($recipient)
            ->post(route('requests.reply', $internalRequest), ['body' => 'Réponse du manager'])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->actingAs($sender)
            ->post(route('requests.reply', $internalRequest), ['body' => 'Merci'])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame(2, $internalRequest->replies()->count());
        $this->assertSame(RequestStatus::ANSWERED, $internalRequest->fresh()->status);
    }

    public function test_only_recipient_can_close_and_closed_requests_reject_all_replies(): void
    {
        $sender = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $recipient = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $internalRequest = $this->requestBetween($sender, $recipient);

        $this->actingAs($sender)
            ->patch(route('requests.close', $internalRequest))
            ->assertForbidden();

        $this->actingAs($recipient)
            ->patch(route('requests.close', $internalRequest))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame(RequestStatus::CLOSED, $internalRequest->fresh()->status);

        $this->get(route('requests.show', $internalRequest))
            ->assertOk()
            ->assertDontSee('Ajouter une réponse');

        $this->post(route('requests.reply', $internalRequest), ['body' => 'Réponse du destinataire'])
            ->assertForbidden();

        $this->actingAs($sender)
            ->post(route('requests.reply', $internalRequest), ['body' => 'Réponse de l’expéditeur'])
            ->assertForbidden();

        $this->assertDatabaseCount('request_replies', 0);
    }

    public function test_subject_and_message_are_required(): void
    {
        $sender = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $recipient = User::factory()->create(['role' => 'manager', 'is_active' => true]);

        $this->actingAs($sender)
            ->post(route('requests.store'), [
                'subject' => '',
                'message' => '',
                'recipient_id' => $recipient->id,
            ])
            ->assertSessionHasErrors(['subject', 'message']);

        $this->assertDatabaseCount('internal_requests', 0);
    }

    public function test_participants_can_view_request_pages(): void
    {
        $sender = User::factory()->create(['role' => 'employee', 'is_active' => true]);
        $recipient = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $internalRequest = $this->requestBetween($sender, $recipient);

        $this->actingAs($recipient)
            ->get(route('requests.index'))
            ->assertOk()
            ->assertSee('Demandes internes')
            ->assertSee('Reçues')
            ->assertSee('Envoyées')
            ->assertSee($internalRequest->subject);

        $this->get(route('requests.create'))
            ->assertOk()
            ->assertSee('Nouvelle demande interne');

        $this->get(route('requests.show', $internalRequest))
            ->assertOk()
            ->assertSee('Fil de réponses')
            ->assertSee($sender->name)
            ->assertSee($recipient->name);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('requests.index'))
            ->assertRedirect(route('login'));
    }

    public function test_inactive_user_is_refused(): void
    {
        $inactiveEmployee = User::factory()->create(['role' => 'employee', 'is_active' => false]);

        $this->actingAs($inactiveEmployee)
            ->get(route('requests.index'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    /** @param array{subject: string, message: string, recipient_id: int} $payload */
    private function payload(int $recipientId): array
    {
        return [
            'subject' => 'Demande de test',
            'message' => 'Message de test',
            'recipient_id' => $recipientId,
        ];
    }

    private function requestBetween(User $sender, User $recipient, RequestStatus $status = RequestStatus::OPEN): InternalRequest
    {
        return InternalRequest::factory()
            ->for($sender, 'sender')
            ->for($recipient, 'recipient')
            ->create(['status' => $status]);
    }
}
