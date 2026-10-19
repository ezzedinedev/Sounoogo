<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Lesson;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        $course->lessons()->create([
            ...$this->validated($request),
            'position' => ($course->lessons()->max('position') ?? 0) + 1,
        ]);

        return back()->with('status', 'Leçon ajoutée.');
    }

    public function update(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $lesson->update($this->validated($request));

        return back()->with('status', 'Leçon enregistrée.');
    }

    public function destroy(Course $course, Lesson $lesson): RedirectResponse
    {
        $lesson->delete();

        return back()->with('status', 'Leçon supprimée.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'content' => ['required', 'string', 'max:50000'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:240'],
            'is_preview' => ['boolean'],
        ]);

        return [...$data, 'is_preview' => $request->boolean('is_preview')];
    }
}
