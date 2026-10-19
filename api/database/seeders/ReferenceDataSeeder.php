<?php

namespace Database\Seeders;

use App\Domain\Billing\Models\Plan;
use App\Domain\Listening\Models\EmergencyContact;
use Illuminate\Database\Seeder;

/** Données de référence nécessaires en production. */
class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        // Numéros publics du Burkina Faso — à confirmer et compléter (ligne d'écoute locale) depuis le back-office.
        $contacts = [
            ['label' => 'Police secours', 'phone' => '17', 'description' => 'Danger immédiat, violences'],
            ['label' => 'Sapeurs-pompiers', 'phone' => '18', 'description' => 'Urgence médicale, secours à personne'],
            ['label' => 'Gendarmerie nationale', 'phone' => '16', 'description' => 'Danger immédiat hors zone urbaine'],
        ];

        foreach ($contacts as $position => $contact) {
            EmergencyContact::updateOrCreate(
                ['country' => 'BF', 'phone' => $contact['phone']],
                [...$contact, 'position' => $position, 'is_active' => true],
            );
        }

        $plans = [
            [
                'code' => 'essentiel',
                'name' => 'Essentiel',
                'description' => 'Toutes les formations confiance en soi.',
                'price' => 2500,
                'includes_courses' => true,
                'consultation_discount' => 0,
                'features' => ['Accès à toutes les formations', 'Nouveaux contenus chaque mois', 'Écoute anonyme toujours gratuite'],
            ],
            [
                'code' => 'serenite',
                'name' => 'Sérénité',
                'description' => 'Formations et consultations à tarif réduit.',
                'price' => 7500,
                'includes_courses' => true,
                'consultation_discount' => 20,
                'features' => ['Tout Essentiel', '-20 % sur les consultations', 'Panels mensuels avec des professionnels'],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['code' => $plan['code']], [...$plan, 'duration_days' => 30, 'is_active' => true]);
        }
    }
}
