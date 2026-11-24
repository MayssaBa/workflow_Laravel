<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public const PROJECT_MEMBER_ERROR = "Cet employé ne fait pas partie du projet. Ajoutez-le d'abord aux membres.";

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

            'project_id' => [
                'nullable',
                'integer',
                'exists:projects,id',
            ],

            'assigned_to' => [
                'nullable',
                'integer',
                self::assignedToProjectMemberRule($this->input('project_id')),
            ],
        ];
    }

    public static function assignedToProjectMemberRule(mixed $projectId): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($projectId): void {
            if ($value === null || $value === '' || ! is_numeric($value)) {
                return;
            }

            $isMember = is_numeric($projectId) && DB::table('project_user')
                ->join('users', 'users.id', '=', 'project_user.user_id')
                ->where('project_user.project_id', $projectId)
                ->where('project_user.user_id', $value)
                ->where('users.role', 'employee')
                ->where('users.is_active', true)
                ->exists();

            if (! $isMember) {
                $fail(self::PROJECT_MEMBER_ERROR);
            }
        };
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
            'project_id.integer' => 'Le projet sélectionné est invalide.',
            'project_id.exists' => 'Le projet sélectionné est invalide.',
            'assigned_to.integer' => "L'employé sélectionné est invalide.",
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
            'project_id' => 'projet',
            'assigned_to' => 'employé',
        ];
    }
}
