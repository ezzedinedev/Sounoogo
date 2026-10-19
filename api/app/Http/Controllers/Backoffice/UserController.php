<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role = Role::tryFrom((string) $request->query('role'));

        $users = User::query()
            ->with('roles')
            ->when($role, fn ($q) => $q->role($role->value))
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($q) => $q
                ->where('pseudonym', 'like', "%{$term}%")
                ->orWhere('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('backoffice.users.index', ['users' => $users, 'role' => $role]);
    }

    public function create(): View
    {
        return view('backoffice.users.create', [
            'roles' => collect(Role::cases())->reject(fn (Role $role) => $role === Role::Member),
        ]);
    }

    /** Création des comptes de l'équipe (écoutants, superviseurs, professionnels, admins). */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'role' => ['required', Rule::enum(Role::class)->except([Role::Member])],
            'password' => ['required', Password::min(10)],
        ]);

        $user = User::create([
            ...$data,
            'pseudonym' => strtok($data['name'], ' '),
            'email_verified_at' => now(),
        ]);
        $user->assignRole($data['role']);

        activity('admin')->performedOn($user)->withProperties(['role' => $data['role']])->log('Compte équipe créé');

        if ($data['role'] === Role::Professional->value) {
            return redirect()->route('backoffice.professionals.create', ['user' => $user->id])
                ->with('status', 'Compte créé. Complète maintenant le profil professionnel.');
        }

        return redirect()->route('backoffice.users.index')->with('status', 'Compte créé.');
    }

    public function toggleSuspension(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 422, 'Tu ne peux pas suspendre ton propre compte.');
        abort_if($user->hasRole(Role::Admin->value) && ! $request->user()->hasRole(Role::Admin->value), 403);

        $suspend = ! $user->isSuspended();
        $user->forceFill(['suspended_at' => $suspend ? now() : null, 'is_available' => false])->save();

        if ($suspend) {
            $user->tokens()->delete();
        }

        activity('moderation')->performedOn($user)->log($suspend ? 'Compte suspendu' : 'Compte réactivé');

        return back()->with('status', $suspend ? 'Compte suspendu.' : 'Compte réactivé.');
    }
}
