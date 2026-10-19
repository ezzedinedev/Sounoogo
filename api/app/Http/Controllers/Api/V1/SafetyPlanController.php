<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Wellbeing\Actions\SaveSafetyPlan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wellbeing\SaveSafetyPlanRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @tags Bien-être
 */
class SafetyPlanController extends Controller
{
    /** Mon plan de sécurité (`data` vaut `null` s'il n'est pas encore rempli). */
    public function show(Request $request): JsonResponse
    {
        $plan = $request->user()->safetyPlan;

        return response()->json(['data' => $plan ? [...$plan->sections(), 'updated_at' => $plan->updated_at] : null]);
    }

    /** Enregistrer le plan de sécurité (remplace la version précédente). */
    public function update(SaveSafetyPlanRequest $request, SaveSafetyPlan $save): JsonResponse
    {
        $plan = $save($request->user(), $request->validated());

        return response()->json(['data' => [...$plan->sections(), 'updated_at' => $plan->updated_at]]);
    }

    public function destroy(Request $request): Response
    {
        $request->user()->safetyPlan()->delete();

        return response()->noContent();
    }
}
