<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\View\View;

class IssueController extends Controller
{
    /**
     * Display a listing of open issues.
     */
    public function index(): View
    {
        $issues = Issue::with([
            'task.project',
            'task.assignee',
        ])
            ->whereIn('status', ['pending', 'open'])
            ->latest()
            ->paginate(10);

        return view('issues.index', compact('issues'));
    }
}
