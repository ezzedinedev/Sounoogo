<?php

namespace App\Support;

/** Évolution en pourcentage entre deux périodes, pour les cartes KPI. */
final readonly class Trend
{
    public function __construct(public int $current, public int $previous) {}

    public function percent(): ?int
    {
        if ($this->previous === 0) {
            return null;
        }

        return (int) round(($this->current - $this->previous) * 100 / $this->previous);
    }

    public function label(): string
    {
        $percent = $this->percent();

        return match (true) {
            $percent === null => $this->current > 0 ? 'Nouveau' : 'Stable',
            $percent > 0 => "+{$percent} %",
            default => "{$percent} %",
        };
    }

    public function icon(): string
    {
        return ($this->percent() ?? 0) < 0 ? 'trending-down' : 'trending-up';
    }
}
