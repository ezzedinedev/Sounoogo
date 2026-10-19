<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Content\Enums\ExerciseCategory;
use App\Domain\Content\Models\Exercise;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    public function index(): View
    {
        return view('backoffice.exercises.index', ['exercises' => Exercise::orderBy('position')->orderBy('id')->get()]);
    }

    public function create(): View
    {
        return view('backoffice.exercises.form', ['exercise' => new Exercise(['category' => ExerciseCategory::Grounding])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $exercise = Exercise::create($this->validated($request));
        activity('contenus')->performedOn($exercise)->log('Exercice créé');

        return redirect()->route('backoffice.exercises.edit', $exercise)->with('status', 'Exercice créé.');
    }

    public function edit(Exercise $exercise): View
    {
        return view('backoffice.exercises.form', ['exercise' => $exercise]);
    }

    public function update(Request $request, Exercise $exercise): RedirectResponse
    {
        $exercise->update($this->validated($request, $exercise));
        activity('contenus')->performedOn($exercise)->log('Exercice modifié');

        return back()->with('status', 'Exercice enregistré.');
    }

    public function destroy(Exercise $exercise): RedirectResponse
    {
        if ($exercise->audio_path) {
            Storage::disk('public')->delete($exercise->audio_path);
        }
        $exercise->delete();
        activity('contenus')->log("Exercice « {$exercise->title} » supprimé");

        return redirect()->route('backoffice.exercises.index')->with('status', 'Exercice supprimé.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Exercise $exercise = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'alpha_dash', 'max:180', Rule::unique('exercises')->ignore($exercise)],
            'summary' => ['required', 'string', 'max:300'],
            'category' => ['required', Rule::enum(ExerciseCategory::class)],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:60'],
            'language' => ['required', Rule::in(['fr', 'mos', 'dyu'])],
            'position' => ['nullable', 'integer', 'min:0'],
            'mode' => ['required', Rule::in(['steps', 'breathing'])],
            'steps_text' => ['required_if:mode,steps', 'nullable', 'string', 'max:6000'],
            'breathing.inhale' => ['required_if:mode,breathing', 'nullable', 'integer', 'between:1,12'],
            'breathing.hold' => ['nullable', 'integer', 'between:0,12'],
            'breathing.exhale' => ['required_if:mode,breathing', 'nullable', 'integer', 'between:1,12'],
            'breathing.rest' => ['nullable', 'integer', 'between:0,12'],
            'breathing.cycles' => ['required_if:mode,breathing', 'nullable', 'integer', 'between:1,40'],
            'audio' => ['nullable', 'file', 'mimetypes:audio/mpeg,audio/mp4,audio/x-m4a,audio/aac,audio/ogg', 'max:20480'],
            'remove_audio' => ['boolean'],
            'published' => ['boolean'],
        ]);

        $attributes = [
            'title' => $data['title'],
            'slug' => ($data['slug'] ?? null) ?: Str::slug($data['title']),
            'summary' => $data['summary'],
            'category' => $data['category'],
            'duration_minutes' => $data['duration_minutes'],
            'language' => $data['language'],
            'position' => $data['position'] ?? 0,
            'published_at' => $request->boolean('published') ? ($exercise->published_at ?? now()) : null,
            'steps' => $data['mode'] === 'steps' ? self::parseSteps($data['steps_text'] ?? '') : null,
            'breathing' => $data['mode'] === 'breathing' ? [
                'inhale' => (int) $data['breathing']['inhale'],
                'hold' => (int) ($data['breathing']['hold'] ?? 0),
                'exhale' => (int) $data['breathing']['exhale'],
                'rest' => (int) ($data['breathing']['rest'] ?? 0),
                'cycles' => (int) $data['breathing']['cycles'],
            ] : null,
        ];

        if ($request->boolean('remove_audio') || $request->hasFile('audio')) {
            if ($exercise?->audio_path) {
                Storage::disk('public')->delete($exercise->audio_path);
            }
            $attributes['audio_path'] = $request->file('audio')?->store('exercises', 'public');
        }

        return $attributes;
    }

    /**
     * Une étape par ligne, durée facultative après une barre : « Inspire lentement | 20 ».
     *
     * @return list<array{text: string, seconds: int}>
     */
    public static function parseSteps(string $text): array
    {
        return collect(preg_split('/\R/', $text) ?: [])
            ->map(fn (string $line) => array_map('trim', explode('|', $line, 2)))
            ->filter(fn (array $parts) => $parts[0] !== '')
            ->map(fn (array $parts) => ['text' => $parts[0], 'seconds' => max(5, min(300, (int) ($parts[1] ?? 30) ?: 30))])
            ->values()
            ->all();
    }

    /** @param  list<array{text: string, seconds: int}>|null  $steps */
    public static function stepsText(?array $steps): string
    {
        return collect($steps ?? [])->map(fn (array $step) => "{$step['text']} | {$step['seconds']}")->implode("\n");
    }
}
