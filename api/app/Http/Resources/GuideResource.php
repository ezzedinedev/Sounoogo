<?php

namespace App\Http\Resources;

use App\Domain\Content\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Guide */
class GuideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'summary' => $this->summary,
            'reading_minutes' => $this->readingMinutes(),
            'body' => $this->when($request->routeIs('api.v1.guides.show'), $this->body),
            'updated_at' => $this->updated_at,
        ];
    }
}
