<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Content\Models\Guide;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuideController extends Controller
{
    public function index(): View
    {
        return view('backoffice.guides.index', ['guides' => Guide::orderBy('position')->orderBy('id')->get()]);
    }

    public function create(): View
    {
        return view('backoffice.guides.form', ['guide' => new Guide]);
    }

    public function store(Request $request): RedirectResponse
    {
        $guide = Guide::create($this->validated($request));
        activity('contenus')->performedOn($guide)->log('Guide créé');

        return redirect()->route('backoffice.guides.edit', $guide)->with('status', 'Guide créé.');
    }

    public function edit(Guide $guide): View
    {
        return view('backoffice.guides.form', ['guide' => $guide]);
    }

    public function update(Request $request, Guide $guide): RedirectResponse
    {
        $guide->update($this->validated($request, $guide));
        activity('contenus')->performedOn($guide)->log('Guide modifié');

        return back()->with('status', 'Guide enregistré.');
    }

    public function destroy(Guide $guide): RedirectResponse
    {
        $guide->delete();
        activity('contenus')->log("Guide « {$guide->title} » supprimé");

        return redirect()->route('backoffice.guides.index')->with('status', 'Guide supprimé.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Guide $guide = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'alpha_dash', 'max:180', Rule::unique('guides')->ignore($guide)],
            'summary' => ['required', 'string', 'max:300'],
            'body' => ['required', 'string', 'max:30000'],
            'position' => ['nullable', 'integer', 'min:0'],
            'published' => ['boolean'],
        ]);

        return [
            'title' => $data['title'],
            'slug' => ($data['slug'] ?? null) ?: Str::slug($data['title']),
            'summary' => $data['summary'],
            'body' => $data['body'],
            'position' => $data['position'] ?? 0,
            'published_at' => $request->boolean('published') ? ($guide->published_at ?? now()) : null,
        ];
    }
}
