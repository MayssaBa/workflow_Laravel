<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display all employees.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        $employees = User::where('role', 'employee')
            ->with(['team.department'])
            ->orderBy('name')
            ->paginate(10);

        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form to create an employee.
     */
    public function create(): View
    {
        Gate::authorize('create', User::class);

        $teams = Team::with('department')
            ->orderBy('name')
            ->get();

        return view('admin.employees.create', compact('teams'));
    }

    /**
     * Store a new employee.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'lowercase', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', 'min:8'],
            'team_id' => ['nullable', 'exists:teams,id'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'employee',
            'is_active' => true,
            'team_id' => $validated['team_id'] ?? null,
        ]);

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Employé ajouté avec succès.');
    }

    /**
     * Display one employee.
     */
    public function show(User $employee): View
    {
        Gate::authorize('view', $employee);

        abort_unless($employee->role === 'employee', 404);

        $employee->load('team.department');

        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form to edit an employee.
     */
    public function edit(User $employee): View
    {
        Gate::authorize('update', $employee);

        abort_unless($employee->role === 'employee', 404);

        $teams = Team::with('department')
            ->orderBy('name')
            ->get();

        return view('admin.employees.edit', compact('employee', 'teams'));
    }

    /**
     * Update an employee.
     */
    public function update(Request $request, User $employee): RedirectResponse
    {
        Gate::authorize('update', $employee);

        abort_unless($employee->role === 'employee', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'lowercase',
                'max:255',
                Rule::unique('users', 'email')->ignore($employee->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $employee->name = $validated['name'];
        $employee->email = $validated['email'];
        $employee->phone = $validated['phone'] ?? null;
        $employee->team_id = $validated['team_id'] ?? null;

        if (!empty($validated['password'])) {
            $employee->password = Hash::make($validated['password']);
        }

        $employee->save();

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Employé modifié avec succès.');
    }

    /**
     * Deactivate an employee instead of deleting the account.
     */
    public function destroy(User $employee): RedirectResponse
    {
        Gate::authorize('update', $employee);

        abort_unless($employee->role === 'employee', 404);

        if ($employee->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $employee->update(['is_active' => false]);

        return redirect()
            ->route('admin.employees.index')
            ->with('success', 'Employé désactivé avec succès.');
    }
}
