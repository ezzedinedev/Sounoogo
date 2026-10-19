<?php

namespace App\Http\Resources;

use App\Domain\Learning\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Certificate */
class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'holder_name' => $this->holder_name,
            'issued_at' => $this->issued_at->toIso8601String(),
            'course' => $this->whenLoaded('course', fn () => ['slug' => $this->course->slug, 'title' => $this->course->title]),
            'pdf_url' => route('api.v1.certificates.pdf', $this->resource),
            'verification_url' => $this->verificationUrl(),
        ];
    }
}
