<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct()
    {
        abort_unless(auth()->check() && auth()->user()->isManager(), 403);
    }

    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function show(Request $request, User $user)
    {
        if ($request->expectsJson()) {
            return response()->json($user->only('id', 'name', 'email', 'role'));
        }
        return redirect()->route('users.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'role'                  => 'required|in:user,manager',
            'password'              => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'User created successfully.', 'id' => $user->id]);
        }

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:user,manager',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->role  = $data['role'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'User updated successfully.', 'id' => $user->id]);
        }

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'You cannot delete your own account.'], 422);
            }
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'User deleted.']);
        }

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
