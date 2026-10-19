<?php

namespace App\Domain\Consultation\Data;

use Illuminate\Support\Carbon;

final readonly class VideoAccess
{
    public function __construct(
        public string $url,
        public Carbon $opensAt,
        public Carbon $closesAt,
    ) {}
}
