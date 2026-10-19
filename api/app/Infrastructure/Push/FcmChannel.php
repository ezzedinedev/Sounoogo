<?php

namespace App\Infrastructure\Push;

use App\Domain\Identity\Models\User;
use Illuminate\Notifications\Notification;

/** Canal de notification Laravel : envoie à tous les appareils du membre et oublie les jetons périmés. */
class FcmChannel
{
    public function __construct(private readonly FcmSender $sender) {}

    public function send(User $user, Notification $notification): void
    {
        if (! FcmSender::enabled() || ! method_exists($notification, 'toPush')) {
            return;
        }

        /** @var PushMessage $message */
        $message = $notification->toPush($user);

        foreach ($user->deviceTokens as $device) {
            $this->sender->send($device->token, $message)
                ? $device->update(['last_used_at' => now()])
                : $device->delete();
        }
    }
}
