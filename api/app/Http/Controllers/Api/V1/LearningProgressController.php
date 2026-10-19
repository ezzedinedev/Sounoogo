<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Learning\Actions\IssueCertificate;
use App\Domain\Learning\Enums\Badge;
use App\Domain\Learning\Models\Certificate;
use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Services\CertificateDocument;
use App\Domain\Learning\Services\LearningProgress;
use App\Http\Controllers\Controller;
use App\Http\Resources\BadgeResource;
use App\Http\Resources\CertificateResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @tags Formations
 */
class LearningProgressController extends Controller
{
    /** Ma progression : série de jours, jalons (obtenus ou non) et attestations. */
    public function show(Request $request, LearningProgress $progress): JsonResponse
    {
        $user = $request->user();
        $awarded = $user->badges()->pluck('awarded_at', 'badge'); // badge (texte) => date d'obtention

        return response()->json(['data' => [
            ...$progress->for($user),
            'badges' => array_map(
                fn (Badge $badge) => BadgeResource::of($badge, $awarded[$badge->value] ?? null),
                Badge::cases(),
            ),
            'certificates' => CertificateResource::collection($user->certificates()->with('course')->latest('issued_at')->get()),
        ]]);
    }

    /** Demander l'attestation d'une formation terminée, avec le nom à y inscrire. */
    public function issue(Request $request, Course $course, IssueCertificate $issue): JsonResponse
    {
        $data = $request->validate([
            'holder_name' => ['required', 'string', 'min:2', 'max:80', 'regex:/^[\pL\pM\s\'’.-]+$/u'],
        ], [
            'holder_name.regex' => 'Le nom ne peut contenir que des lettres, des espaces, des tirets et des apostrophes.',
        ]);

        $enrollment = $request->user()->enrollments()->where('course_id', $course->id)->firstOrFail();
        $certificate = $issue($enrollment, $data['holder_name']);

        return (new CertificateResource($certificate->load('course')))
            ->response()
            ->setStatusCode($certificate->wasRecentlyCreated ? 201 : 200);
    }

    /** Télécharger l'attestation (PDF). */
    public function pdf(Request $request, Certificate $certificate, CertificateDocument $document): Response
    {
        abort_unless($certificate->user_id === $request->user()->id, 404);

        return $document->render($certificate)->download($document->filename($certificate));
    }
}
