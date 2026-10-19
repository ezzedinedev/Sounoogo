<?php

use App\Domain\Consultation\Models\ProfessionalProfile;
use Database\Seeders\DemoSeeder;

beforeEach(fn () => $this->seed(DemoSeeder::class));

it('affiche les pages du site vitrine', function (string $uri) {
    $this->get($uri)->assertOk()->assertSee('SUNOOGO');
})->with(['/', '/professionnels', '/formations', '/urgence', '/confidentialite', '/paiement/succes', '/paiement/echec']);

it('affiche les numéros d\'urgence sur toutes les pages', function () {
    $this->get('/formations')->assertSee('Police secours')->assertSee('tel:17', false);
});

it('filtre l\'annuaire des professionnels', function () {
    $this->get('/professionnels?format=in_person')
        ->assertSee('Aminata Traoré')
        ->assertDontSee('Jean-Baptiste Compaoré');
});

it('n\'affiche que les professionnels vérifiés', function () {
    ProfessionalProfile::query()->update(['verified_at' => null]);

    $this->get('/professionnels')->assertSee('Aucun professionnel ne correspond');
});
