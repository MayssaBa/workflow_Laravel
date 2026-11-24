<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttachProjectMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project && $this->user()?->can('manageMembers', $project) === true;
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
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function (Builder $query): void {
                    $query->where('role', 'employee')
                        ->where('is_active', true);
                }),
                Rule::unique('project_user', 'user_id')->where(function (Builder $query) use ($project): void {
                    $query->where('project_id', $project->id);
                }),
            ],
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
            'user_id.required' => 'Veuillez sélectionner un employé.',
            'user_id.integer' => 'La sélection de l’employé est invalide.',
            'user_id.exists' => 'Seuls les employés actifs peuvent être ajoutés.',
            'user_id.unique' => 'Cet employé est déjà membre du projet.',
        ];
    }
}
