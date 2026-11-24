<?php

namespace Tests\Unit\Models;

use App\Models\TaskHistory;
use PHPUnit\Framework\TestCase;

class TaskHistoryTest extends TestCase
{
    public function test_changes_formats_creation_values_and_ignores_technical_fields(): void
    {
        $history = new TaskHistory([
            'action' => 'created',
            'new_value' => json_encode([
                'id' => 7,
                'title' => 'Titre initial',
                'description' => '',
                'priority' => 'high',
                'due_date' => '2026-11-24',
                'project_id' => 3,
                'assigned_to' => 2,
                'status' => 'todo',
                'created_by' => 1,
                'created_at' => '2026-11-24 10:00:00',
                'updated_at' => '2026-11-24 10:00:00',
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $history->withNameMaps(
            ['3' => 'Projet Alpha'],
            ['2' => 'Alice Martin']
        );

        $this->assertSame([
            ['Titre', null, 'Titre initial'],
            ['Priorité', null, 'Haute'],
            ['Échéance', null, '24/11/2026'],
            ['Projet', null, 'Projet Alpha'],
            ['Assignée à', null, 'Alice Martin'],
            ['Statut', null, 'À faire'],
        ], $history->changes());
    }

    public function test_changes_formats_update_values_with_fr_labels(): void
    {
        $history = new TaskHistory([
            'action' => 'closed',
            'field' => 'status',
            'old_value' => 'in_progress',
            'new_value' => 'done',
        ]);

        $this->assertSame([
            ['Statut', 'En cours', 'Terminée'],
        ], $history->changes());
    }

    public function test_changes_keeps_invalid_json_as_plain_text(): void
    {
        $history = new TaskHistory([
            'action' => 'updated',
            'field' => 'description',
            'old_value' => '{invalid',
            'new_value' => 'Texte corrigé',
        ]);

        $this->assertSame([
            ['Description', '{invalid', 'Texte corrigé'],
        ], $history->changes());
    }

    public function test_action_labels_cover_required_actions_and_default(): void
    {
        $this->assertSame('a créé la tâche', (new TaskHistory(['action' => 'created']))->actionLabel());
        $this->assertSame('a assigné la tâche', (new TaskHistory(['action' => 'assigned']))->actionLabel());
        $this->assertSame('a clôturé la tâche', (new TaskHistory(['action' => 'closed']))->actionLabel());
        $this->assertSame('a réorganisé la tâche', (new TaskHistory(['action' => 'reorganized']))->actionLabel());
        $this->assertSame('a modifié la tâche', (new TaskHistory(['action' => 'unknown']))->actionLabel());
    }
}
