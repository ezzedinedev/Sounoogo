<?php

use App\Domain\Consultation\Models\AvailabilitySlot;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Consultation\Notifications\BookingReminder;
use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\DeviceToken;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Notifications\ListenerReplied;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

function fakeFirebaseCredentials(): void
{
    $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($key, $pem);
    config(['sunoogo.push.credentials' => base64_encode(json_encode([
        'project_id' => 'sunoogo-test',
        'client_email' => 'push@sunoogo-test.iam.gserviceaccount.com',
        'private_key' => $pem,
        'token_uri' => 'https://oauth2.googleapis.com/token',
    ]))]);
}

it('envoie la notification à chaque appareil et oublie les jetons périmés', function () {
    fakeFirebaseCredentials();
    Http::fake([
        'oauth2.googleapis.com/*' => Http::response(['access_token' => 'ya29.test', 'expires_in' => 3600]),
        'fcm.googleapis.com/*' => Http::sequence()
            ->push(['name' => 'projects/sunoogo-test/messages/1'])
            ->push(['error' => ['status' => 'NOT_FOUND', 'details' => [['errorCode' => 'UNREGISTERED']]]], 404),
    ]);
    $member = userWithRole(Role::Member);
    DeviceToken::create(['user_id' => $member->id, 'token' => 'valide', 'platform' => 'android']);
    DeviceToken::create(['user_id' => $member->id, 'token' => 'perime', 'platform' => 'ios']);
    $conversation = Conversation::create(['member_id' => $member->id]);

    $member->notify(new ListenerReplied($conversation));

    expect(DeviceToken::pluck('token')->all())->toBe(['valide']);
    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'projects/sunoogo-test/messages:send')
        && $request['message']['notification']['title'] === 'Nouveau message'
        && $request['message']['android']['collapse_key'] === 'conversation-'.$conversation->id);
});

it('rappelle une consultation confirmée une heure avant, une seule fois', function () {
    Notification::fake();
    $member = userWithRole(Role::Member);
    $pro = userWithRole(Role::Professional);
    $profile = $pro->professionalProfile()->create(['title' => 'Psychologue', 'specialty' => 'Anxiété', 'session_price' => 5000, 'session_minutes' => 50, 'modes' => ['video']]);
    $slot = AvailabilitySlot::create(['professional_profile_id' => $profile->id, 'starts_at' => now()->addMinutes(50), 'ends_at' => now()->addMinutes(100)]);
    Booking::create(['member_id' => $member->id, 'professional_profile_id' => $profile->id, 'availability_slot_id' => $slot->id, 'mode' => 'video', 'status' => 'confirmed', 'price' => 5000]);

    $this->artisan('bookings:send-reminders')->assertSuccessful();
    $this->artisan('bookings:send-reminders')->assertSuccessful();

    Notification::assertSentToTimes($member, BookingReminder::class, 1);
});
