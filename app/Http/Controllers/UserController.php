<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->latest()
            ->get();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user)],
            'password' => ['nullable', 'string', 'confirmed', 'min:8'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        $shouldBeAdmin = $request->boolean('is_admin');

        if ($user->is_admin && ! $shouldBeAdmin && User::where('is_admin', true)->count() === 1) {
            return back()->withErrors([
                'is_admin' => 'At least one admin account must remain active.',
            ])->withInput();
        }

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'is_admin' => $shouldBeAdmin,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $user->update($payload);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account from staff management.');
        }

        if ($user->is_admin && User::where('is_admin', true)->count() === 1) {
            return back()->with('error', 'At least one admin account must remain active.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
