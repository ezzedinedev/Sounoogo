<?php

namespace App\Http\Controllers\Site;

use App\Domain\Billing\Models\Plan;
use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Domain\Learning\Models\Course;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function home(): View
    {
        return view('site.home', [
            'professionals' => ProfessionalProfile::listed()->with('user')->inRandomOrder()->limit(3)->get(),
            'courses' => $this->publishedCourses()->limit(3)->get(),
            'plans' => Plan::where('is_active', true)->orderBy('price')->get(),
            'faq' => [
                ['Est-ce vraiment anonyme ?', 'Oui. Tu n\'as besoin d\'aucune information personnelle pour parler à un écoutant. Tu reçois un pseudonyme, et les messages sont chiffrés dans notre base de données.'],
                ['Qui sont les écoutants ?', 'Des bénévoles et salariés formés à l\'écoute active, encadrés par des superviseurs cliniques. Ils ne donnent pas de diagnostic : ils t\'écoutent et t\'aident à y voir plus clair.'],
                ['Que se passe-t-il si je ne vais vraiment pas bien ?', 'Le bouton SOS affiche les numéros d\'urgence et prévient un superviseur en temps réel. Si tes messages montrent que tu es en danger, le superviseur est aussi alerté automatiquement.'],
                ['Combien coûte une consultation ?', 'Chaque professionnel fixe son tarif, affiché avant la réservation. Avec l\'abonnement Sérénité, tu bénéficies de 20 % de réduction.'],
                ['Comment payer ?', 'Par Wave ou Orange Money, directement depuis l\'application. Aucune carte bancaire n\'est nécessaire.'],
            ],
        ]);
    }

    public function professionals(Request $request): View
    {
        $professionals = ProfessionalProfile::listed()
            ->with('user')
            ->when($request->query('specialite'), fn ($query, $term) => $query->where(fn ($q) => $q
                ->where('specialty', 'like', "%{$term}%")
                ->orWhere('title', 'like', "%{$term}%")))
            ->when($request->query('format'), fn ($query, $mode) => $query->whereJsonContains('modes', $mode))
            ->orderBy('session_price')
            ->paginate(12)
            ->withQueryString();

        return view('site.professionals', ['professionals' => $professionals]);
    }

    public function courses(): View
    {
        return view('site.courses', ['courses' => $this->publishedCourses()->get()]);
    }

    public function emergency(): View
    {
        return view('site.emergency');
    }

    public function privacy(): View
    {
        return view('site.privacy');
    }

    public function paymentResult(Request $request, string $result): View
    {
        return view('site.payment-result', [
            'succeeded' => $result === 'succes',
            'reference' => $request->query('ref'),
        ]);
    }

    private function publishedCourses()
    {
        return Course::published()
            ->withCount('lessons')
            ->withSum('lessons', 'duration_minutes')
            ->latest('published_at');
    }
}
