<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    /**
     * Vérifier que seul un manager peut créer une tâche.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isManager();
    }

    /**
     * Règles de validation des données.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'priority' => [
                'required',
                Rule::in(['low', 'medium', 'high', 'urgent']),
            ],

            'due_date' => [
                'nullable',
                'date',
            ],
        ];
    }

    /**
     * Messages de validation en français.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre de la tâche est obligatoire.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'description.string' => 'La description doit être un texte.',
            'priority.required' => 'La priorité est obligatoire.',
            'priority.in' => 'La priorité sélectionnée est invalide.',
            'due_date.date' => 'La date limite est invalide.',
        ];
    }

    /**
     * Noms des champs en français.
     */
    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'description' => 'description',
            'priority' => 'priorité',
            'due_date' => 'date limite',
        ];
    }
}
