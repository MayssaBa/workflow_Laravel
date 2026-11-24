<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project && $this->user()?->can('update', $project) === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Project $project */
        $project = $this->route('project');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects', 'name')
                    ->where(function (Builder $query): void {
                        $query->where('manager_id', $this->user()?->id)
                            ->whereNull('deleted_at');
                    })
                    ->ignore($project->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_date' => ['nullable', 'date', 'required_with:end_date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du projet est obligatoire.',
            'name.string' => 'Le nom du projet doit être une chaîne de caractères.',
            'name.max' => 'Le nom du projet ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Vous avez déjà un projet portant ce nom.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne doit pas dépasser 1000 caractères.',
            'start_date.date' => 'La date de début est invalide.',
            'start_date.required_with' => 'La date de début est obligatoire lorsque la date de fin est renseignée.',
            'end_date.date' => 'La date de fin est invalide.',
            'end_date.after' => 'La date de fin doit être postérieure à la date de début.',
        ];
    }
}
