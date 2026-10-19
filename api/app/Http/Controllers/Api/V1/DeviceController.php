<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Identity\Models\DeviceToken;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * @tags Notifications
 */
class DeviceController extends Controller
{
    /** Inscrire cet appareil aux notifications push (jeton Firebase). */
    public function store(Request $request): Response
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'platform' => ['required', Rule::in(['android', 'ios', 'web'])],
        ]);

        // Un téléphone partagé change de compte : le jeton suit la personne connectée.
        DeviceToken::updateOrCreate(['token' => $data['token']], ['user_id' => $request->user()->id, 'platform' => $data['platform'], 'last_used_at' => now()]);

        return response()->noContent();
    }

    /** Désinscrire cet appareil (déconnexion, notifications désactivées). */
    public function destroy(Request $request): Response
    {
        $request->validate(['token' => ['required', 'string', 'max:255']]);
        $request->user()->deviceTokens()->where('token', $request->input('token'))->delete();

        return response()->noContent();
    }
}
