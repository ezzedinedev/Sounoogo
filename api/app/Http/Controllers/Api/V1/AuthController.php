<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Identity\Actions\CreateAnonymousMember;
use App\Domain\Identity\Actions\RegisterMember;
use App\Domain\Identity\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AnonymousRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RecoverRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @tags Authentification
 */
class AuthController extends Controller
{
    /**
     * Accès anonyme (niveau 1).
     *
     * Aucune donnée identifiante. Le code de récupération n'est renvoyé qu'une seule fois.
     *
     * @unauthenticated
     */
    public function anonymous(AnonymousRequest $request, CreateAnonymousMember $create): JsonResponse
    {
        [$user, $recoveryCode] = $create($request->validated('country'), $request->validated('locale'));

        return $this->tokenResponse($user, $request->validated('device_name'), ['recovery_code' => $recoveryCode], 201);
    }

    /**
     * Retrouver un compte anonyme avec son code de récupération.
     *
     * @unauthenticated
     */
    public function recover(RecoverRequest $request): JsonResponse
    {
        $code = strtoupper(trim($request->validated('recovery_code')));

        $user = User::query()
            ->where('pseudonym', $request->validated('pseudonym'))
            ->where('is_anonymous', true)
            ->whereNotNull('recovery_code_hash')
            ->get()
            ->first(fn (User $candidate) => Hash::check($code, $candidate->recovery_code_hash));

        if ($user === null) {
            throw ValidationException::withMessages(['recovery_code' => 'Pseudonyme ou code de récupération invalide.']);
        }

        return $this->tokenResponse($user, $request->validated('device_name'));
    }

    /**
     * Créer un compte avec identifiants.
     *
     * @unauthenticated
     */
    public function register(RegisterRequest $request, RegisterMember $register): JsonResponse
    {
        $user = $register($request->validated());

        return $this->tokenResponse($user, $request->validated('device_name'), status: 201);
    }

    /**
     * Connexion par e-mail ou téléphone.
     *
     * @unauthenticated
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $login = $request->validated('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::firstWhere($field, $login);

        if ($user === null || $user->password === null || ! Hash::check($request->validated('password'), $user->password)) {
            throw ValidationException::withMessages(['login' => 'Identifiants incorrects.']);
        }

        if ($user->isSuspended()) {
            throw ValidationException::withMessages(['login' => 'Ce compte est suspendu.']);
        }

        return $this->tokenResponse($user, $request->validated('device_name'));
    }

    /** Déconnexion de l'appareil courant. */
    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    private function tokenResponse(User $user, ?string $device, array $extra = [], int $status = 200): JsonResponse
    {
        $token = $user->createToken($device ?: 'mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
            ...$extra,
        ], $status);
    }
}
