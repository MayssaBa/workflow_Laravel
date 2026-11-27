<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Models\Department;
use App\Models\InternalRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;

class DashboardService
{
    public function employeeDashboard(User $user): array
    {
        return [
            'myTasks' => Task::where('assigned_to', $user->id)->count(),

            'myPendingTasks' => Task::where('assigned_to', $user->id)
                ->whereIn('status', ['todo', 'in_progress'])
                ->count(),

            'myOverdueTasks' => Task::where('assigned_to', $user->id)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', now()->toDateString())
                ->where('status', '!=', 'done')
                ->count(),
        ];
    }

    public function managerDashboard(): array
    {
        return [
            'activeProjects' => Project::count(),

            'overdueTasks' => Task::whereNotNull('due_date')
                ->whereDate('due_date', '<', now()->toDateString())
                ->where('status', '!=', 'done')
                ->count(),

            'openIssues' => Issue::whereIn('status', ['pending', 'open'])
                ->count(),

            'openRequests' => InternalRequest::where(
                'status',
                RequestStatus::OPEN->value
            )->count(),

            'projectProgress' => Project::withCount([
                'tasks',
                'tasks as completed_tasks_count' => function ($query) {
                    $query->where('status', 'done');
                },
            ])->get(),

            'employeeWorkload' => User::where('role', 'employee')
                ->where('is_active', true)
                ->withCount([
                    'assignedTasks as active_tasks_count' => function ($query) {
                        $query->whereIn('status', [
                            'todo',
                            'in_progress',
                            'blocked',
                        ]);
                    },
                ])
                ->orderByDesc('active_tasks_count')
                ->get(),
        ];
    }

    public function adminDashboard(): array
    {
        return [
            'activeEmployees' => User::where('role', 'employee')
                ->where('is_active', true)
                ->count(),

            'departments' => Department::count(),

            'teams' => Team::count(),

            'openRequests' => InternalRequest::where(
                'status',
                RequestStatus::OPEN->value
            )->count(),

            'employeesByDepartment' => Department::withCount('teams')
                ->with([
                    'teams.users' => function ($query) {
                        $query->where('role', 'employee')
                            ->where('is_active', true);
                    },
                ])
                ->get(),

            'requestsByStatus' => InternalRequest::selectRaw(
                'status, COUNT(*) as total'
            )
                ->groupBy('status')
                ->get(),
        ];
    }
}
