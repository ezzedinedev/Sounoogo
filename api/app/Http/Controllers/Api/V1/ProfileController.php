<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Identity\Actions\RegisterMember;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @tags Profil
 */
class ProfileController extends Controller
{
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function update(UpdateProfileRequest $request): UserResource
    {
        $request->user()->update($request->validated());

        return new UserResource($request->user());
    }

    /** Transformer le compte anonyme en compte avec identifiants (l'historique est conservé). */
    public function upgrade(RegisterRequest $request, RegisterMember $register): UserResource
    {
        abort_unless($request->user()->is_anonymous, 409, 'Ce compte a déjà des identifiants.');

        return new UserResource($register($request->validated(), $request->user()));
    }

    /** Droit à l'effacement : supprime le compte et toutes ses données. */
    public function destroy(Request $request): Response
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->delete();

        return response()->noContent();
    }
}
