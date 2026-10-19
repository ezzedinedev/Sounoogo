<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Listening\Models\EmergencyContact;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmergencyContactController extends Controller
{
    public function index(): View
    {
        return view('backoffice.emergency-contacts.index', [
            'contacts' => EmergencyContact::orderBy('country')->orderBy('position')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        EmergencyContact::create($this->validated($request));
        activity('admin')->log('Numéro d\'urgence ajouté');

        return back()->with('status', 'Numéro ajouté.');
    }

    public function update(Request $request, EmergencyContact $contact): RedirectResponse
    {
        $contact->update($this->validated($request));
        activity('admin')->performedOn($contact)->log('Numéro d\'urgence modifié');

        return back()->with('status', 'Numéro enregistré.');
    }

    public function destroy(EmergencyContact $contact): RedirectResponse
    {
        $contact->delete();
        activity('admin')->log("Numéro d'urgence {$contact->phone} supprimé");

        return back()->with('status', 'Numéro supprimé.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'country' => ['required', 'string', 'size:2'],
            'label' => ['required', 'string', 'max:80'],
            'phone' => ['required', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:160'],
            'position' => ['required', 'integer', 'min:0', 'max:99'],
            'is_active' => ['boolean'],
        ]);

        return [...$data, 'country' => strtoupper($data['country']), 'is_active' => $request->boolean('is_active')];
    }
}
