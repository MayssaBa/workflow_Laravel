<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskHistoryDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_page_renders_history_timeline_without_raw_values(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'is_active' => true]);
        $employee = User::factory()->create([
            'name' => 'Employée Assignée',
            'role' => 'employee',
            'is_active' => true,
        ]);

        $task = Task::factory()->create([
            'title' => 'Tâche de démonstration',
            'created_by' => $manager->id,
            'assigned_to' => $employee->id,
        ]);

        $response = $this->actingAs($manager)
            ->get(route('tasks.show', $task));

        $response->assertOk()
            ->assertSee('Historique de la tâche')
            ->assertSee('Tâche de démonstration')
            ->assertSee('Employée Assignée')
            ->assertSee('a créé la tâche')
            ->assertDontSee('Old value')
            ->assertDontSee('New value')
            ->assertDontSee('created_at')
            ->assertDontSee('{"');

        $historyCard = $this->historyCardHtml($response->getContent());

        $this->assertStringContainsString('Historique de la tâche', $historyCard);
        $this->assertStringNotContainsString('{', $historyCard);
    }

    private function historyCardHtml(string $html): string
    {
        $document = new DOMDocument;

        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();

        $nodes = (new DOMXPath($document))->query('//*[@id="task-history"]');
        $node = $nodes === false ? null : $nodes->item(0);

        return $node === null ? '' : (string) $document->saveHTML($node);
    }
}
