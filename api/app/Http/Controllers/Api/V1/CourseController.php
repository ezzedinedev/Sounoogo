<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Billing\Actions\InitiatePayment;
use App\Domain\Learning\Actions\CompleteLesson;
use App\Domain\Learning\Actions\EnrollInCourse;
use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Lesson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\PayRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @tags Formations
 */
class CourseController extends Controller
{
    /**
     * Catalogue des formations (niveau 3).
     *
     * Filtre : `filter[level]`.
     *
     * @unauthenticated
     */
    public function index(): AnonymousResourceCollection
    {
        $courses = QueryBuilder::for(Course::query()->published()->withCount('lessons')->withSum('lessons', 'duration_minutes'))
            ->allowedFilters('level')
            ->defaultSort('-published_at')
            ->paginate(20)
            ->withQueryString();

        return CourseResource::collection($courses);
    }

    /** @unauthenticated */
    public function show(Course $course): CourseResource
    {
        abort_unless($course->published_at?->isPast(), 404);

        return new CourseResource($course->load('lessons')->loadCount('lessons')->loadSum('lessons', 'duration_minutes'));
    }

    /** S'inscrire : immédiat si la formation est gratuite ou incluse dans l'abonnement, sinon 402. */
    public function enroll(Request $request, Course $course, EnrollInCourse $enroll): EnrollmentResource
    {
        abort_unless($course->published_at?->isPast(), 404);

        return new EnrollmentResource($enroll($request->user(), $course)->load('course'));
    }

    /** Acheter la formation à l'unité. */
    public function purchase(PayRequest $request, Course $course, InitiatePayment $initiate, EnrollInCourse $enroll): PaymentResource
    {
        abort_unless($course->published_at?->isPast(), 404);
        abort_if($course->isFree(), 409, 'Cette formation est gratuite.');
        abort_if($enroll->accessFor($request->user(), $course) !== null, 409, 'Tu as déjà accès à cette formation.');

        return new PaymentResource($initiate($request->user(), $course, $course->price, $request->provider(), $request->phone()));
    }

    /** Contenu d'une leçon (inscrits, ou leçon d'aperçu). */
    public function lesson(Request $request, Course $course, Lesson $lesson): LessonResource
    {
        abort_unless($lesson->course_id === $course->id, 404);

        $enrolled = $request->user()->enrollments()->where('course_id', $course->id)->exists();
        abort_unless($enrolled || $lesson->is_preview, 403, 'Inscris-toi à la formation pour accéder à cette leçon.');

        return (new LessonResource($lesson))->withContent();
    }

    public function completeLesson(Request $request, Course $course, Lesson $lesson, CompleteLesson $complete): EnrollmentResource
    {
        $enrollment = $request->user()->enrollments()->where('course_id', $course->id)->firstOrFail();

        return new EnrollmentResource($complete($enrollment, $lesson)->load('course'));
    }

    /** Mes formations et ma progression. */
    public function enrollments(Request $request): AnonymousResourceCollection
    {
        return EnrollmentResource::collection(
            $request->user()->enrollments()->with('course')->latest()->get()
        );
    }
}
