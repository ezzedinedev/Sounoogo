<?php

use App\Domain\Listening\Enums\RiskLevel;
use App\Domain\Listening\Services\RiskDetector;
use Tests\TestCase;

uses(TestCase::class);

it('évalue le niveau de risque d\'un message', function (string $text, RiskLevel $expected) {
    expect(app(RiskDetector::class)->assess($text))->toBe($expected);
})->with([
    ['Bonjour, j\'ai passé une mauvaise journée', RiskLevel::None],
    ['Je suis vraiment à bout en ce moment', RiskLevel::Medium],
    ['JE VEUX ME SUICIDER', RiskLevel::High],
    ['j’ai plus envie de vivre', RiskLevel::High],
    ['Je pense à mettre fin a mes jours', RiskLevel::High],
]);
