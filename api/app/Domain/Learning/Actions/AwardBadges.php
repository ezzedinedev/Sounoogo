<?php

namespace App\Domain\Learning\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Learning\Enums\Badge;
use App\Domain\Learning\Models\UserBadge;
use App\Domain\Learning\Services\LearningProgress;

/** Décerne les jalons nouvellement atteints et les renvoie (pour les célébrer dans l'app). */
final class AwardBadges
{
    public function __construct(private readonly LearningProgress $progress) {}

    /** @return list<Badge> */
    public function __invoke(User $user): array
    {
        $stats = $this->progress->for($user);
        $owned = UserBadge::where('user_id', $user->id)->pluck('badge')->all();

        $earned = array_values(array_filter(
            Badge::cases(),
            fn (Badge $badge) => ! in_array($badge, $owned, true)
                && $badge->isReachedBy($stats['lessons'], $stats['courses'], $stats['best_streak']),
        ));

        foreach ($earned as $badge) {
            UserBadge::firstOrCreate(['user_id' => $user->id, 'badge' => $badge], ['awarded_at' => now()]);
        }

        return $earned;
    }
}
