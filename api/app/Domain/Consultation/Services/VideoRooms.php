<?php

namespace App\Domain\Consultation\Services;

use App\Domain\Consultation\Contracts\VideoConferencing;
use App\Domain\Consultation\Data\VideoAccess;
use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Enums\ConsultationMode;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Identity\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Salles de téléconsultation : une salle au nom imprévisible par réservation, ouverte
 * de quelques minutes avant le rendez-vous jusqu'à un peu après sa fin.
 */
final class VideoRooms
{
    public function __construct(private readonly VideoConferencing $video) {}

    /** La réservation se déroule-t-elle en visio sur la plateforme ? */
    public function applies(Booking $booking): bool
    {
        return $this->video->isEnabled()
            && $booking->mode === ConsultationMode::Video
            && $booking->status === BookingStatus::Confirmed
            && $booking->slot !== null;
    }

    /** @return array{Carbon, Carbon} ouverture et fermeture de la salle */
    public function window(Booking $booking): array
    {
        return [
            $booking->slot->starts_at->copy()->subMinutes((int) config('sunoogo.video.opens_before_minutes')),
            $booking->slot->ends_at->copy()->addMinutes((int) config('sunoogo.video.closes_after_minutes')),
        ];
    }

    public function isOpen(Booking $booking): bool
    {
        if (! $this->applies($booking)) {
            return false;
        }

        [$opensAt, $closesAt] = $this->window($booking);

        return now()->between($opensAt, $closesAt);
    }

    public function join(Booking $booking, User $user): VideoAccess
    {
        abort_unless($this->applies($booking), 409, 'Cette consultation ne se déroule pas en visio.');

        [$opensAt, $closesAt] = $this->window($booking);
        abort_if(now()->lt($opensAt), 409, 'La salle ouvrira le '.$opensAt->translatedFormat('j F à H:i').'.');
        abort_if(now()->gt($closesAt), 410, 'Cette consultation est terminée.');

        if ($booking->video_room === null) {
            $booking->forceFill(['video_room' => 'sunoogo-'.Str::lower(Str::random(32))])->save();
        }

        $isProfessional = $booking->professional->user_id === $user->id;

        return new VideoAccess(
            url: $this->video->joinUrl($booking->video_room, $this->displayName($booking, $isProfessional), moderator: $isProfessional),
            opensAt: $opensAt,
            closesAt: $closesAt,
        );
    }

    /** Le membre reste sous pseudonyme s'il l'a demandé à la réservation. */
    private function displayName(Booking $booking, bool $isProfessional): string
    {
        if ($isProfessional) {
            return $booking->professional->user->displayName();
        }

        return $booking->keep_anonymous
            ? $booking->member->pseudonym
            : ($booking->member->name ?? $booking->member->pseudonym);
    }
}
