<?php

namespace App\Domain\Reporting\Data;

/**
 * Un chiffre d'un rapport d'impact. Les effectifs de 1 à (seuil − 1) sont masqués pour qu'aucune
 * personne ne puisse être reconnue ; les moyennes et médianes calculées sur trop peu de personnes aussi.
 */
final readonly class Figure
{
    public function __construct(
        public string $label,
        public int|float|null $value,
        public bool $suppressed = false,
        public string $unit = '',
        public ?string $hint = null,
    ) {}

    /** Effectif (nombre de personnes ou d'événements). */
    public static function count(string $label, int $value, ?string $hint = null): self
    {
        return new self($label, $value, self::isSmall($value), hint: $hint);
    }

    /** Statistique calculée sur $population personnes ou événements. */
    public static function statistic(string $label, int|float|null $value, int $population, string $unit = '', ?string $hint = null): self
    {
        return new self($label, $value, $value !== null && $population < self::threshold(), $unit, $hint);
    }

    public function display(): string
    {
        return match (true) {
            $this->suppressed => '< '.self::threshold(),
            $this->value === null => '—',
            is_float($this->value) => number_format($this->value, 1, ',', ' ').$this->unit,
            default => number_format($this->value, 0, ',', ' ').$this->unit,
        };
    }

    /** Valeur exportable : null si masquée. */
    public function exportValue(): int|float|null
    {
        return $this->suppressed ? null : $this->value;
    }

    private static function isSmall(int $value): bool
    {
        return $value > 0 && $value < self::threshold();
    }

    private static function threshold(): int
    {
        return (int) config('sunoogo.impact.min_cell_size', 5);
    }
}
