<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Database\Factories\TaskHistoryFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Throwable;

class TaskHistory extends Model
{
    /** @use HasFactory<TaskHistoryFactory> */
    use HasFactory;

    private const ACTION_LABELS = [
        'created' => 'a créé la tâche',
        'assigned' => 'a assigné la tâche',
        'closed' => 'a clôturé la tâche',
        'reorganized' => 'a réorganisé la tâche',
    ];

    private const FIELD_LABELS = [
        'title' => 'Titre',
        'description' => 'Description',
        'priority' => 'Priorité',
        'due_date' => 'Échéance',
        'project_id' => 'Projet',
        'assigned_to' => 'Assignée à',
        'status' => 'Statut',
    ];

    private const IGNORED_FIELDS = [
        'id',
        'created_at',
        'updated_at',
        'created_by',
        'deleted_at',
    ];

    /** @var array<string, string>|null */
    private ?array $projectNames = null;

    /** @var array<string, string>|null */
    private ?array $userNames = null;

    protected $table = 'task_histories';

    protected $fillable = [
        'task_id',
        'user_id',
        'action',
        'field',
        'old_value',
        'new_value',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function actionLabel(): string
    {
        return self::ACTION_LABELS[$this->action] ?? 'a modifié la tâche';
    }

    /**
     * @return array<int, array{0: string, 1: string|null, 2: string|null}>
     */

    public function changes(): array
    {
        $changes = [];

        foreach ($this->decodedValues() as $field => $newValue) {
            $field = (string) $field;

            if (
                in_array($field, self::IGNORED_FIELDS, true)
                || !array_key_exists($field, self::FIELD_LABELS)
            ) {
                continue;
            }

            $formattedNewValue = $this->formatValue($field, $newValue);
            $formattedOldValue = null;

            if ($this->action !== 'created') {
                $oldValue = $this->decodeValue($this->old_value);
                $formattedOldValue = $this->formatValue($field, $oldValue);
            }

            $changes[] = [
                self::FIELD_LABELS[$field],
                $formattedOldValue ?? 'Aucune valeur',
                $formattedNewValue ?? 'Aucune valeur',
            ];
        }

        return $changes;
    }


    /**
     * @param  array<string, string>  $projectNames
     * @param  array<string, string>  $userNames
     */
    public function withNameMaps(array $projectNames, array $userNames): self
    {
        $this->projectNames = $projectNames;
        $this->userNames = $userNames;

        return $this;
    }

    public static function primeNameMaps(Collection $histories): void
    {
        $projectIds = [];
        $userIds = [];

        foreach ($histories as $history) {
            $projectIds = array_merge($projectIds, $history->referenceIds('project_id'));
            $userIds = array_merge($userIds, $history->referenceIds('assigned_to'));
        }

        $projectNames = [];
        foreach (Project::whereIn('id', array_values(array_unique($projectIds)))->pluck('name', 'id') as $id => $name) {
            $projectNames[(string) $id] = $name;
        }

        $userNames = [];
        foreach (User::whereIn('id', array_values(array_unique($userIds)))->pluck('name', 'id') as $id => $name) {
            $userNames[(string) $id] = $name;
        }

        foreach ($histories as $history) {
            $history->withNameMaps($projectNames, $userNames);
        }
    }

    /**
     * @return array<int, string>
     */
    private function referenceIds(string $field): array
    {
        $values = [];

        if ($this->action === 'created' || $this->field === $field) {
            $values[] = $this->decodeValue($this->new_value);

            if ($this->action !== 'created') {
                $values[] = $this->decodeValue($this->old_value);
            }
        }

        $ids = [];

        foreach ($values as $value) {
            if (is_array($value)) {
                if (array_key_exists($field, $value) && !$this->isBlank($value[$field])) {
                    $ids[] = (string) $value[$field];
                }

                continue;
            }

            if (!$this->isBlank($value)) {
                $ids[] = (string) $value;
            }
        }

        return $ids;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodedValues(): array
    {
        $newValue = $this->decodeValue($this->new_value);

        if ($this->action === 'created' || $this->isBlank($this->field)) {
            return is_array($newValue) ? $newValue : [];
        }

        return [(string) $this->field => $newValue];
    }

    private function decodeValue(?string $value): mixed
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    private function formatValue(string $field, mixed $value): ?string
    {
        if ($this->isBlank($value) || !is_scalar($value)) {
            return null;
        }

        $value = (string) $value;

        return match ($field) {
            'priority' => TaskPriority::tryFrom($value)?->label() ?? $value,
            'status' => TaskStatus::tryFrom($value)?->label() ?? $value,
            'due_date' => $this->formatDate($value),
            'project_id' => $this->projectNames[$value] ?? 'Projet supprimé',
            'assigned_to' => $this->userNames[$value] ?? 'Employé supprimé',
            default => $value,
        };
    }

    private function formatDate(string $value): string
    {
        try {
            return Carbon::parse($value)->format('d/m/Y');
        } catch (Throwable) {
            return $value;
        }
    }

    private function isBlank(mixed $value): bool
    {
        return $value === null
            || (is_string($value) && trim($value) === '')
            || (is_array($value) && $value === []);
    }
}
