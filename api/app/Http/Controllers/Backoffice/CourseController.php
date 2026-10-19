<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Learning\Models\Course;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        return view('backoffice.courses.index', [
            'courses' => Course::withCount(['lessons', 'enrollments'])->latest()->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('backoffice.courses.form', ['course' => new Course(['level' => 'beginner', 'price' => 0, 'included_in_subscription' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $course = Course::create($this->validated($request));

        activity('contenus')->performedOn($course)->log('Formation créée');

        return redirect()->route('backoffice.courses.edit', $course)->with('status', 'Formation créée. Ajoute maintenant ses leçons.');
    }

    public function edit(Course $course): View
    {
        return view('backoffice.courses.form', ['course' => $course->load('lessons')]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $course->update($this->validated($request, $course));

        activity('contenus')->performedOn($course)->log('Formation modifiée');

        return back()->with('status', 'Formation enregistrée.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        abort_if($course->enrollments()->exists(), 422, 'Des membres suivent cette formation : dépublie-la plutôt que de la supprimer.');

        $course->delete();
        activity('contenus')->log("Formation « {$course->title} » supprimée");

        return redirect()->route('backoffice.courses.index')->with('status', 'Formation supprimée.');
    }

    private function validated(Request $request, ?Course $course = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'alpha_dash', 'max:180', Rule::unique('courses')->ignore($course)],
            'summary' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string', 'max:10000'],
            'cover_path' => ['nullable', 'string', 'max:255'],
            'level' => ['required', Rule::in(array_keys(Course::LEVELS))],
            'price' => ['required', 'integer', 'min:0', 'max:1000000'],
            'included_in_subscription' => ['boolean'],
            'published' => ['boolean'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['included_in_subscription'] = $request->boolean('included_in_subscription');
        $data['published_at'] = $request->boolean('published') ? ($course?->published_at ?? now()) : null;
        unset($data['published']);

        return $data;
    }
}
