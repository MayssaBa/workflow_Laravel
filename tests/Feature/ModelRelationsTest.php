<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Department;
use App\Models\Issue;
use App\Models\Task;
use App\Models\TaskHistory;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_belongs_to_its_task_and_author(): void
    {
        $task = Task::factory()->create();
        $author = User::factory()->create();

        $comment = Comment::factory()->for($task)->for($author, 'user')->create();

        $this->assertSame($task->id, $comment->task->id);
        $this->assertSame($author->id, $comment->user->id);
    }

    public function test_issue_belongs_to_its_task_and_resolver(): void
    {
        $task = Task::factory()->create();
        $resolver = User::factory()->create();

        $issue = Issue::factory()->for($task)->for($resolver, 'resolver')->create();

        $this->assertSame($task->id, $issue->task->id);
        $this->assertSame($resolver->id, $issue->resolver->id);
        $this->assertSame('open', $issue->status);
    }

    public function test_task_history_belongs_to_its_task_and_user(): void
    {
        $task = Task::factory()->create();
        $user = User::factory()->create();

        $history = TaskHistory::factory()->for($task)->for($user)->create();

        $this->assertSame($task->id, $history->task->id);
        $this->assertSame($user->id, $history->user->id);
    }

    public function test_department_has_teams_which_have_users(): void
    {
        $department = Department::factory()->create(['name' => 'Ressources humaines']);
        $team = Team::factory()->for($department)->create(['name' => 'Recrutement']);
        $user = User::factory()->create(['team_id' => $team->id]);

        $this->assertTrue($department->teams->contains($team));
        $this->assertSame($department->id, $team->department->id);
        $this->assertTrue($team->users->contains($user));
        $this->assertSame($team->id, $user->team->id);
    }
}
