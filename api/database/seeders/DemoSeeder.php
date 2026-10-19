<?php

namespace Database\Seeders;

use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use App\Domain\Learning\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/** Comptes et contenus de démonstration — jamais en production. */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->staff('admin@sunoogo.local', 'Awa Ouédraogo', Role::Admin);
        $this->staff('supervisor@sunoogo.local', 'Issa Sawadogo', Role::Supervisor);
        $this->staff('listener@sunoogo.local', 'Mariam Kaboré', Role::Listener)->update(['is_available' => true]);
        $this->staff('listener2@sunoogo.local', 'Boris Zongo', Role::Listener);

        $member = User::updateOrCreate(['email' => 'member@sunoogo.local'], [
            'pseudonym' => 'Baobab-204',
            'password' => 'password',
            'is_anonymous' => false,
        ]);
        $member->syncRoles([Role::Member->value]);

        $this->professionals();
        $this->courses();
    }

    private function staff(string $email, string $name, Role $role): User
    {
        $user = User::updateOrCreate(['email' => $email], [
            'name' => $name,
            'pseudonym' => Str::before($name, ' '),
            'password' => 'password',
            'email_verified_at' => now(),
        ]);
        $user->syncRoles([$role->value]);

        return $user;
    }

    private function professionals(): void
    {
        $pros = [
            ['pro@sunoogo.local', 'Dr Aminata Traoré', 'Psychologue clinicienne', 'Anxiété, dépression, traumatismes', 'Ouagadougou', 15000, ['in_person', 'phone', 'video'], ['Français', 'Mooré']],
            ['pro2@sunoogo.local', 'Jean-Baptiste Compaoré', 'Conseiller conjugal et familial', 'Couple, famille, dépendance affective', 'Bobo-Dioulasso', 10000, ['phone', 'video'], ['Français', 'Dioula']],
            ['pro3@sunoogo.local', 'Salimata Diallo', 'Coach certifiée', 'Confiance en soi, estime de soi', 'Ouagadougou', 8000, ['in_person', 'video'], ['Français', 'Fulfuldé']],
        ];

        foreach ($pros as [$email, $name, $title, $specialty, $city, $price, $modes, $languages]) {
            $user = $this->staff($email, $name, Role::Professional);

            $profile = ProfessionalProfile::updateOrCreate(['user_id' => $user->id], [
                'title' => $title,
                'specialty' => $specialty,
                'bio' => "{$title} à {$city}. Accompagnement bienveillant et confidentiel, dans le respect de ton rythme.",
                'city' => $city,
                'session_price' => $price,
                'session_minutes' => 50,
                'modes' => $modes,
                'languages' => $languages,
                'verified_at' => now(),
                'is_active' => true,
            ]);

            if ($profile->slots()->doesntExist()) {
                foreach (range(1, 10) as $day) {
                    foreach ([9, 14, 16] as $hour) {
                        $start = Carbon::tomorrow()->addDays($day - 1)->setTime($hour, 0);
                        if ($start->isWeekend()) {
                            continue;
                        }
                        $profile->slots()->create(['starts_at' => $start, 'ends_at' => $start->copy()->addMinutes(50)]);
                    }
                }
            }
        }
    }

    private function courses(): void
    {
        $courses = [
            [
                'title' => 'Reprendre confiance en soi',
                'summary' => 'Six étapes pour identifier tes forces, apaiser ta voix critique et oser t\'affirmer.',
                'level' => 'beginner',
                'price' => 0,
                'cover' => 'images/courses/confiance.jpg',
                'lessons' => ['Comprendre l\'estime de soi', 'Repérer sa voix critique', 'Lister ses réussites', 'Poser ses limites', 'Oser dire non', 'Bilan et prochaines étapes'],
            ],
            [
                'title' => 'Sortir de la dépendance affective',
                'summary' => 'Comprendre ses attachements, retrouver son autonomie émotionnelle et construire des relations saines.',
                'level' => 'intermediate',
                'price' => 5000,
                'cover' => 'images/courses/independance.jpg',
                'lessons' => ['Qu\'est-ce que la dépendance affective ?', 'Les schémas d\'attachement', 'La peur de l\'abandon', 'Se reconnecter à ses besoins', 'Construire son autonomie', 'Des relations équilibrées'],
            ],
            [
                'title' => 'Gérer le stress au quotidien',
                'summary' => 'Des outils simples et concrets — respiration, organisation, ancrage — pour alléger la pression.',
                'level' => 'beginner',
                'price' => 3000,
                'cover' => 'images/courses/stress.jpg',
                'lessons' => ['Le stress, ami ou ennemi ?', 'La respiration carrée', 'Organiser ses journées', 'Le sommeil réparateur', 'Ancrage et pleine conscience'],
            ],
        ];

        foreach ($courses as $data) {
            $course = Course::updateOrCreate(['slug' => Str::slug($data['title'])], [
                'title' => $data['title'],
                'summary' => $data['summary'],
                'description' => $data['summary']."\n\nCe parcours a été conçu avec des professionnels de la santé mentale. Avance à ton rythme : chaque leçon dure quelques minutes et se termine par un petit exercice.",
                'level' => $data['level'],
                'price' => $data['price'],
                'cover_path' => $data['cover'],
                'included_in_subscription' => true,
                'published_at' => now()->subDays(7),
            ]);

            if ($course->lessons()->doesntExist()) {
                foreach ($data['lessons'] as $position => $title) {
                    $course->lessons()->create([
                        'title' => $title,
                        'content' => "## {$title}\n\nContenu de la leçon à rédiger avec l'équipe clinique.\n\n**Exercice :** prends cinq minutes pour noter ce que cette leçon t'inspire.",
                        'position' => $position + 1,
                        'duration_minutes' => 6 + $position,
                        'is_preview' => $position === 0,
                    ]);
                }
            }
        }
    }
}
