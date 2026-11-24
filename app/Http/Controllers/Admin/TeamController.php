<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeamController extends Controller
{
    /**
     * Afficher la liste des équipes.
     */
    public function index(): View
    {
        $teams = Team::with('department')
            ->withCount('users')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.teams.index', compact('teams'));
    }

    /**
     * Afficher le formulaire d'ajout.
     */
    public function create(): View
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.teams.create', compact('departments'));
    }

    /**
     * Enregistrer une nouvelle équipe.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:teams,name',
            ],
            'department_id' => [
                'required',
                'exists:departments,id',
            ],
        ]);

        Team::create($validated);

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Équipe ajoutée avec succès.');
    }

    /**
     * Consulter une équipe.
     */
    public function show(Team $team): View
    {
        $team->load(['department', 'users']);

        return view('admin.teams.show', compact('team'));
    }

    /**
     * Afficher le formulaire de modification.
     */
    public function edit(Team $team): View
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.teams.edit', compact('team', 'departments'));
    }

    /**
     * Modifier une équipe.
     */
    public function update(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('teams', 'name')->ignore($team->id),
            ],
            'department_id' => [
                'required',
                'exists:departments,id',
            ],
        ]);

        $team->update($validated);

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Équipe modifiée avec succès.');
    }

    /**
     * Supprimer une équipe.
     */
    public function destroy(Team $team): RedirectResponse
    {
        if ($team->users()->exists()) {
            return redirect()
                ->route('admin.teams.index')
                ->with('error', 'Impossible de supprimer cette équipe : elle contient encore des employés.');
        }

        $team->delete();

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Équipe supprimée avec succès.');
    }
}
