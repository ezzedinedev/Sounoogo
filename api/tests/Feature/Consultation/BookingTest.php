<?php

use App\Domain\Billing\Models\Plan;
use App\Domain\Consultation\Models\AvailabilitySlot;
use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Domain\Identity\Enums\Role;
use Laravel\Sanctum\Sanctum;

function professionalWithSlot(array $profile = []): array
{
    $pro = userWithRole(Role::Professional, ['name' => 'Dr Test']);
    $profile = ProfessionalProfile::create([
        'user_id' => $pro->id,
        'title' => 'Psychologue',
        'specialty' => 'Anxiété',
        'session_price' => 10000,
        'modes' => ['phone', 'video'],
        'verified_at' => now(),
        ...$profile,
    ]);
    $slot = $profile->slots()->create(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour()]);

    return [$pro, $profile, $slot];
}

it('liste uniquement les professionnels vérifiés', function () {
    professionalWithSlot();
    professionalWithSlot(['verified_at' => null]);

    $this->getJson('/api/v1/professionals')->assertOk()->assertJsonCount(1, 'data');
});

it('réserve un créneau, paie, puis confirme la consultation', function () {
    [, , $slot] = professionalWithSlot();
    actingAsRole(Role::Member);

    $booking = $this->postJson('/api/v1/bookings', ['slot_id' => $slot->id, 'mode' => 'phone'])
        ->assertCreated()
        ->assertJsonPath('data.status', 'pending_payment')
        ->assertJsonPath('data.price', 10000)
        ->json('data');

    expect($slot->fresh()->is_booked)->toBeTrue();

    $payment = $this->postJson("/api/v1/bookings/{$booking['id']}/pay", ['provider' => 'mock'])
        ->assertCreated()
        ->json('data');

    $this->getJson("/api/v1/payments/mock/{$payment['reference']}?status=succeeded")->assertOk();

    $this->getJson("/api/v1/bookings/{$booking['id']}")->assertJsonPath('data.status', 'confirmed');
    $this->getJson("/api/v1/payments/{$payment['reference']}")->assertJsonPath('data.status', 'succeeded');
});

it('refuse un créneau déjà réservé', function () {
    [, , $slot] = professionalWithSlot();
    $slot->update(['is_booked' => true]);
    actingAsRole(Role::Member);

    $this->postJson('/api/v1/bookings', ['slot_id' => $slot->id, 'mode' => 'phone'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('slot_id');
});

it('refuse un format de consultation non proposé', function () {
    [, , $slot] = professionalWithSlot();
    actingAsRole(Role::Member);

    $this->postJson('/api/v1/bookings', ['slot_id' => $slot->id, 'mode' => 'in_person'])
        ->assertJsonValidationErrors('mode');
});

it('applique la remise de l\'abonnement', function () {
    [, , $slot] = professionalWithSlot();
    $member = actingAsRole(Role::Member);
    $member->subscriptions()->create([
        'plan_id' => Plan::firstWhere('code', 'serenite')->id,
        'status' => 'active',
        'starts_at' => now(),
        'ends_at' => now()->addMonth(),
    ]);

    $this->postJson('/api/v1/bookings', ['slot_id' => $slot->id, 'mode' => 'video'])
        ->assertJsonPath('data.price', 8000);
});

it('libère le créneau à l\'annulation', function () {
    [, , $slot] = professionalWithSlot();
    actingAsRole(Role::Member);
    $id = $this->postJson('/api/v1/bookings', ['slot_id' => $slot->id, 'mode' => 'phone'])->json('data.id');

    $this->postJson("/api/v1/bookings/{$id}/cancel")->assertOk()->assertJsonPath('data.status', 'cancelled');

    expect($slot->fresh()->is_booked)->toBeFalse();
});

it('masque l\'identité du membre au professionnel si l\'anonymat est demandé', function () {
    [$pro, , $slot] = professionalWithSlot();
    $member = actingAsRole(Role::Member, ['name' => 'Identité Cachée', 'phone' => '+22670000001']);
    $id = $this->postJson('/api/v1/bookings', ['slot_id' => $slot->id, 'mode' => 'phone', 'keep_anonymous' => true])->json('data.id');

    Sanctum::actingAs($pro);

    $this->getJson("/api/v1/bookings/{$id}")
        ->assertOk()
        ->assertJsonPath('data.member.display_name', $member->pseudonym)
        ->assertJsonMissingPath('data.member.phone');
});

it('permet au professionnel de gérer ses créneaux', function () {
    [$pro] = professionalWithSlot();
    Sanctum::actingAs($pro);

    $this->postJson('/api/v1/pro/slots', ['slots' => [
        ['starts_at' => now()->addDays(2)->toIso8601String(), 'ends_at' => now()->addDays(2)->addHour()->toIso8601String()],
    ]])->assertCreated()->assertJsonCount(1, 'data');

    $this->getJson('/api/v1/pro/slots')->assertJsonCount(2, 'data');

    $slotId = AvailabilitySlot::latest('id')->value('id');
    $this->deleteJson("/api/v1/pro/slots/{$slotId}")->assertNoContent();
});
