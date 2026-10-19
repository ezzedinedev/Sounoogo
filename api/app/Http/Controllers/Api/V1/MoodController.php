<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Wellbeing\Actions\RecordMood;
use App\Domain\Wellbeing\Services\MoodInsights;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wellbeing\RecordMoodRequest;
use App\Http\Resources\MoodEntryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Bien-être
 */
class MoodController extends Controller
{
    /**
     * Journal d'humeur.
     *
     * Les check-ins des `days` derniers jours (30 par défaut, 90 au plus) et leur lecture :
     * moyenne, régularité, jours d'humeur basse et suggestion (`talk`, `exercise` ou `null`).
     */
    public function index(Request $request, MoodInsights $insights): JsonResponse
    {
        $days = min(max((int) $request->query('days', 30), 7), 90);
        $entries = $insights->history($request->user(), $days);

        return response()->json([
            'data' => MoodEntryResource::collection($entries),
            'summary' => $insights->summarize($entries),
        ]);
    }

    /** Check-in du jour (remplace celui déjà fait aujourd'hui). */
    public function store(RecordMoodRequest $request, RecordMood $record, MoodInsights $insights): JsonResponse
    {
        $entry = $record($request->user(), $request->mood(), $request->feelings(), $request->validated('note'));

        return response()->json([
            'data' => new MoodEntryResource($entry),
            'summary' => $insights->summarize($insights->history($request->user(), 30)),
        ], $entry->wasRecentlyCreated ? 201 : 200);
    }
}
