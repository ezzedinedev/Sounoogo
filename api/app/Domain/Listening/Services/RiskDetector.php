<?php

namespace App\Domain\Listening\Services;

use App\Domain\Listening\Enums\RiskLevel;
use Illuminate\Support\Str;

class RiskDetector
{
    public function assess(string $text): RiskLevel
    {
        $normalized = $this->normalize($text);

        foreach ([RiskLevel::High, RiskLevel::Medium] as $level) {
            foreach (config("sunoogo.risk_keywords.{$level->value}", []) as $keyword) {
                if (str_contains($normalized, $this->normalize($keyword))) {
                    return $level;
                }
            }
        }

        return RiskLevel::None;
    }

    private function normalize(string $text): string
    {
        return Str::of($text)->ascii()->lower()->replace(['’', '`'], "'")->squish()->toString();
    }
}
