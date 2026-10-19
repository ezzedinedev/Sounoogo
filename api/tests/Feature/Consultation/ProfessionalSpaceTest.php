<?php

use App\Domain\Consultation\Enums\PayoutStatus;
use App\Domain\Consultation\Models\AvailabilitySlot;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Consultation\Models\Payout;
use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Domain\Consultation\Notifications\ProfessionalBookingReminder;
use App\Domain\Identity\Enums\Role;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;

function professional(array $profile = []): ProfessionalProfile
{
    return userWithRole(Role::Professional, ['email' => 'pro-'.uniqid().'@sunoogo.test'])->professionalProfile()->create([
        'title' => 'Psychologue', 'specialty' => 'Anxiété', 'session_price' => 10000, 'session_minutes' => 50, 'modes' => ['video'],
        ...$profile,
    ]);
}

function confirmedBooking(ProfessionalProfile $profile, Carbon $start, array $attributes = []): Booking
{
    $slot = AvailabilitySlot::create(['professional_profile_id' => $profile->id, 'starts_at' => $start, 'ends_at' => $start->copy()->addMinutes(50), 'is_booked' => true]);

    return Booking::create([
        'member_id' => userWithRole(Role::Member)->id, 'professional_profile_id' => $profile->id, 'availability_slot_id' => $slot->id,
        'mode' => 'video', 'status' => 'confirmed', 'price' => 10000, ...$attributes,
    ]);
}

/** @return array<string, mixed> */
function jwtPayload(string $url): array
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
    [$header, $payload, $signature] = explode('.', $query['jwt']);
    $expected = rtrim(strtr(base64_encode(hash_hmac('sha256', "{$header}.{$payload}", 'secret-test', true)), '+/', '-_'), '=');
    expect($signature)->toBe($expected);

    return json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
}

describe('téléconsultation', function () {
    beforeEach(fn () => config([
        'sunoogo.video.base_url' => 'https://visio.sunoogo.test',
        'sunoogo.video.app_id' => 'sunoogo',
        'sunoogo.video.app_secret' => 'secret-test',
    ]));

    it('n\'ouvre la salle qu\'autour de l\'heure du rendez-vous', function () {
        $booking = confirmedBooking(professional(), now()->addHour());
        Sanctum::actingAs($booking->member);

        $this->getJson("/api/v1/bookings/{$booking->id}")
            ->assertJsonPath('data.video.is_open', false)
            ->assertJsonPath('data.video.opens_at', now()->addMinutes(50)->toIso8601String());
        $this->getJson("/api/v1/bookings/{$booking->id}/video")->assertStatus(409);

        $this->travel(55)->minutes();
        $url = $this->getJson("/api/v1/bookings/{$booking->id}/video")->assertOk()->json('data.url');

        expect($url)->toStartWith('https://visio.sunoogo.test/sunoogo-')
            ->and(jwtPayload($url))->toMatchArray(['aud' => 'jitsi', 'iss' => 'sunoogo', 'sub' => 'visio.sunoogo.test', 'room' => $booking->fresh()->video_room])
            ->and(jwtPayload($url)['context']['user'])->toBe(['name' => $booking->member->pseudonym, 'moderator' => false]);

        $this->travel(3)->hours();
        $this->getJson("/api/v1/bookings/{$booking->id}/video")->assertStatus(410);
    });

    it('donne la même salle aux deux participants, le professionnel en modérateur', function () {
        $profile = professional();
        $booking = confirmedBooking($profile, now()->addMinutes(5), ['keep_anonymous' => false]);

        Sanctum::actingAs($booking->member);
        $memberUrl = $this->getJson("/api/v1/bookings/{$booking->id}/video")->json('data.url');
        Sanctum::actingAs($profile->user);
        $proUrl = $this->getJson("/api/v1/bookings/{$booking->id}/video")->json('data.url');

        expect(jwtPayload($memberUrl)['room'])->toBe(jwtPayload($proUrl)['room'])
            ->and(jwtPayload($proUrl)['context']['user']['moderator'])->toBeTrue();

        $this->actingAs($profile->user)->get("/admin/agenda/consultations/{$booking->id}/visio")
            ->assertRedirectContains('https://visio.sunoogo.test/'.$booking->fresh()->video_room);
    });

    it('ferme la salle aux autres membres', function () {
        $booking = confirmedBooking(professional(), now()->addMinutes(5));
        actingAsRole(Role::Member);

        $this->getJson("/api/v1/bookings/{$booking->id}/video")->assertForbidden();
    });

    it('n\'expose pas de visio quand Jitsi n\'est pas configuré', function () {
        config(['sunoogo.video.base_url' => '']);
        $booking = confirmedBooking(professional(), now()->addMinutes(5));
        Sanctum::actingAs($booking->member);

        $this->getJson("/api/v1/bookings/{$booking->id}")->assertJsonMissingPath('data.video');
        $this->getJson("/api/v1/bookings/{$booking->id}/video")->assertStatus(409);
    });
});

it('fige la part du professionnel à la fin de la consultation', function () {
    config(['sunoogo.payouts.commission_percent' => 15]);
    $profile = professional();
    $booking = confirmedBooking($profile, now()->subHour());

    $this->actingAs($profile->user)->post("/admin/agenda/consultations/{$booking->id}/terminer")->assertRedirect();

    expect($booking->fresh())->status->value->toBe('completed')->professional_share->toBe(8500);

    // Un changement de commission ultérieur ne touche pas les consultations passées.
    config(['sunoogo.payouts.commission_percent' => 30]);
    $this->actingAs($profile->user)->get('/admin/revenus')->assertOk()->assertSee('8 500 F');
});

it('verse au professionnel son solde, sans jamais le dépasser', function () {
    config(['sunoogo.payouts.minimum_amount' => 5000]);
    $profile = professional();
    confirmedBooking($profile, now()->subDay(), ['status' => 'completed', 'completed_at' => now(), 'professional_share' => 9000]);

    $this->actingAs($profile->user)->post('/admin/revenus/versements', ['amount' => 9500, 'phone' => '+226 70 11 22 33'])->assertSessionHasErrors('amount');
    $this->actingAs($profile->user)->post('/admin/revenus/versements', ['amount' => 3000, 'phone' => '+22670112233'])->assertSessionHasErrors('amount');
    $this->actingAs($profile->user)->post('/admin/revenus/versements', ['amount' => 6000, 'phone' => '+226 70 11 22 33'])->assertSessionHasNoErrors();

    $payout = Payout::sole();
    expect($payout)->phone->toBe('+22670112233')->status->toBe(PayoutStatus::Requested);

    // Le solde restant (3 000 F) est sous le minimum : nouvelle demande impossible.
    $this->actingAs($profile->user)->post('/admin/revenus/versements', ['amount' => 3000, 'phone' => '+22670112233'])->assertSessionHasErrors('amount');

    $admin = userWithRole(Role::Admin);
    $this->actingAs($admin)->patch("/admin/versements/{$payout->id}", ['status' => 'paid'])->assertSessionHasErrors('reference');
    $this->actingAs($admin)->patch("/admin/versements/{$payout->id}", ['status' => 'paid', 'reference' => 'OM-123'])->assertSessionHasNoErrors();

    expect($payout->fresh())->status->toBe(PayoutStatus::Paid)->processed_by->toBe($admin->id);
    $this->actingAs($admin)->patch("/admin/versements/{$payout->id}", ['status' => 'rejected', 'note' => 'Doublon'])->assertStatus(409);
});

it('réserve les versements à l\'administration', function () {
    $this->actingAs(userWithRole(Role::Supervisor))->get('/admin/versements')->assertForbidden();
    $this->actingAs(professional()->user)->get('/admin/versements')->assertForbidden();
});

it('crée une série de créneaux sans doublon ni chevauchement', function () {
    $this->travelTo(Carbon::parse('2026-11-02 08:00')); // un lundi
    $profile = professional();
    $profile->slots()->create(['starts_at' => '2026-11-03 09:30', 'ends_at' => '2026-11-03 10:20']);

    $this->actingAs($profile->user)->post('/admin/agenda/creneaux/serie', [
        'from' => '2026-11-02', 'weeks' => 2, 'weekdays' => [2, 4], 'times' => ['09:00', '14:00', ''],
    ])->assertSessionHas('status', '7 créneaux ajoutés.');

    // 2 semaines × 2 jours × 2 heures = 8, moins le mardi 9 h qui chevauche le créneau existant.
    expect($profile->slots()->count())->toBe(8);
});

it('prévient aussi le professionnel par e-mail avant la consultation', function () {
    Notification::fake();
    $profile = professional();
    confirmedBooking($profile, now()->addMinutes(45));

    $this->artisan('bookings:send-reminders')->assertSuccessful();

    Notification::assertSentTo($profile->user, ProfessionalBookingReminder::class);
});

it('masque les petits effectifs du rapport d\'impact', function () {
    $member = userWithRole(Role::Member);
    foreach (range(1, 6) as $i) {
        $member->memberConversations()->create(['topic' => 'stress']);
    }
    $member->memberConversations()->create(['topic' => 'grief']);

    $this->actingAs(userWithRole(Role::Supervisor))->get('/admin/impact?periode=mois')
        ->assertOk()
        ->assertSeeInOrder(['Conversations ouvertes', '7'])
        ->assertSeeInOrder(['Personnes écoutées', '&lt; 5'], false)
        ->assertSeeInOrder(['Stress', '6', 'Deuil', '&lt; 5'], false);

    $csv = $this->actingAs(userWithRole(Role::Supervisor))->get('/admin/impact/export.csv?periode=mois')->assertOk()->streamedContent();
    expect($csv)->toContain('Écoute;"Conversations ouvertes";7;;non')
        ->toContain('Écoute;"Personnes écoutées";;;oui');

    $this->actingAs(userWithRole(Role::Listener))->get('/admin/impact')->assertForbidden();
});

it('exporte le rapport d\'impact en PDF', function () {
    $this->actingAs(userWithRole(Role::Admin))->get('/admin/impact/export.pdf')->assertOk()->assertHeader('content-type', 'application/pdf');
});
