<?php

namespace App\Http\Requests;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $sender = $this->user();

        return $sender !== null && ($sender->isEmployee() || $sender->isManager());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject' => [
                'required',
                'string',
                'max:255',
            ],

            'message' => [
                'required',
                'string',
                'max:5000',
            ],

            'recipient_id' => [
                'required',
                'integer',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $sender = $this->user();

                    if ($sender === null || ! is_numeric($value)) {
                        return;
                    }

                    $recipient = User::find($value);

                    $allowedRoles = match (true) {
                        $sender->isEmployee() => ['manager', 'admin'],
                        $sender->isManager() => ['admin'],
                        default => [],
                    };

                    if ($recipient === null
                        || ! $recipient->isActive()
                        || ! in_array($recipient->role, $allowedRoles, true)
                        || $recipient->id === $sender->id) {
                        $fail('Le destinataire sélectionné est invalide.');
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'subject.required' => 'Le sujet de la demande est obligatoire.',
            'subject.string' => 'Le sujet de la demande doit être un texte.',
            'subject.max' => 'Le sujet ne doit pas dépasser 255 caractères.',
            'message.required' => 'Le message de la demande est obligatoire.',
            'message.string' => 'Le message de la demande doit être un texte.',
            'message.max' => 'Le message ne doit pas dépasser 5000 caractères.',
            'recipient_id.required' => 'Le destinataire est obligatoire.',
            'recipient_id.integer' => 'Le destinataire sélectionné est invalide.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'subject' => 'sujet',
            'message' => 'message',
            'recipient_id' => 'destinataire',
        ];
    }
}
