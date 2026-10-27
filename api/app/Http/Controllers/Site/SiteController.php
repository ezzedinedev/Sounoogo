<?php

namespace App\Http\Controllers\Site;

use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Domain\Learning\Models\Certificate;
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
            'faq' => [
                ['Est-ce vraiment anonyme ?', 'Oui. Tu n\'as besoin d\'aucune information personnelle pour parler à un écoutant. Tu reçois un pseudonyme, et les messages sont chiffrés dans notre base de données.'],
                ['Qui sont les écoutants ?', 'Des bénévoles et salariés formés à l\'écoute active, encadrés par des superviseurs cliniques. Ils ne donnent pas de diagnostic : ils t\'écoutent et t\'aident à y voir plus clair.'],
                ['Que se passe-t-il si je ne vais vraiment pas bien ?', 'Le bouton SOS affiche les numéros d\'urgence et prévient un superviseur en temps réel. Si tes messages montrent que tu es en danger, le superviseur est aussi alerté automatiquement.'],
                ['Est-ce vraiment gratuit ?', 'Oui. Tous les services SUNOOGO sont gratuits : l\'écoute, les consultations et les parcours.'],
                ['Comment prendre rendez-vous ?', 'Choisis un professionnel et un créneau dans l\'application. Tu peux rester anonyme.'],
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
            ->orderBy('title')
            ->orderBy('id')
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

    /** Vérification publique d'une attestation par son code. */
    public function certificate(Certificate $certificate): View
    {
        return view('site.certificate', ['certificate' => $certificate->load('course')]);
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
