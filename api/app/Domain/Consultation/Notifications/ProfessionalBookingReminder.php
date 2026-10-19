<?php

namespace App\Domain\Consultation\Notifications;

use App\Domain\Consultation\Models\Booking;
use App\Domain\Identity\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Rappel au professionnel, par e-mail, une heure avant la consultation. Aucune donnée de santé dans le message. */
class ProfessionalBookingReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    /** @return list<string> */
    public function via(User $notifiable): array
    {
        return $notifiable->email ? ['mail'] : [];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $start = $this->booking->slot?->starts_at;

        return (new MailMessage)
            ->subject('Consultation à '.$start?->format('H:i'))
            ->greeting('Bonjour '.$notifiable->displayName().',')
            ->line('Ta consultation '.mb_strtolower($this->booking->mode->label()).' commence le '.$start?->translatedFormat('l j F à H:i').'.')
            ->action('Ouvrir mon agenda', route('backoffice.agenda.index'))
            ->line('La salle de visio s\'ouvre dix minutes avant le rendez-vous.')
            ->salutation('L\'équipe SUNOOGO');
    }
}
