<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MenuPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhere('department', 'like', "%{$s}%"));
        }

        $users = $query->latest()->paginate(25)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        // Get all available roles from menu_permissions
        $existingRoles = MenuPermission::select('role')->distinct()->pluck('role')->toArray();

        // If no roles defined yet, use default roles
        if (empty($existingRoles)) {
            $existingRoles = ['admin', 'user', 'manager'];
        }

        $roles = array_combine($existingRoles, array_map('ucfirst', $existingRoles));
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|max:30',
            'department' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Get all available roles from menu_permissions
        $existingRoles = MenuPermission::select('role')->distinct()->pluck('role')->toArray();

        // If no roles defined yet, use default roles
        if (empty($existingRoles)) {
            $existingRoles = ['admin', 'user', 'manager'];
        }

        $roles = array_combine($existingRoles, array_map('ucfirst', $existingRoles));
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|max:30',
            'department' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
