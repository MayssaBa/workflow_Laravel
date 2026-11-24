<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Afficher la liste des départements.
     */
    public function index(): View
    {
        $departments = Department::withCount('teams')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Afficher le formulaire d'ajout.
     */
    public function create(): View
    {
        return view('admin.departments.create');
    }

    /**
     * Enregistrer un nouveau département.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:departments,name',
            ],
        ]);

        Department::create($validated);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Département ajouté avec succès.');
    }

    /**
     * Consulter un département.
     */
    public function show(Department $department): View
    {
        $department->load('teams');

        return view('admin.departments.show', compact('department'));
    }

    /**
     * Afficher le formulaire de modification.
     */
    public function edit(Department $department): View
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Modifier un département.
     */
    public function update(
        Request $request,
        Department $department
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')->ignore($department->id),
            ],
        ]);

        $department->update($validated);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Département modifié avec succès.');
    }

    /**
     * Supprimer un département.
     */
    public function destroy(Department $department): RedirectResponse
    {
        if ($department->teams()->exists()) {
            return redirect()
                ->route('admin.departments.index')
                ->with('error', 'Impossible de supprimer ce département : il contient encore des équipes.');
        }

        $department->delete();

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Département supprimé avec succès.');
    }
}
