<?php

namespace App\Domain\Wellbeing\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Wellbeing\Models\SafetyPlan;

final class SaveSafetyPlan
{
    /** @param  array<string, list<mixed>>  $sections */
    public function __invoke(User $user, array $sections): SafetyPlan
    {
        $content = collect($sections)
            ->only([...SafetyPlan::TEXT_SECTIONS, ...SafetyPlan::CONTACT_SECTIONS])
            ->map(fn (array $items, string $section) => collect($items)
                ->map(fn ($item) => in_array($section, SafetyPlan::TEXT_SECTIONS, true)
                    ? trim((string) $item)
                    : ['name' => trim((string) $item['name']), 'phone' => preg_replace('/[\s.\-]/', '', (string) $item['phone'])])
                ->filter()
                ->values()
                ->all())
            ->all();

        return SafetyPlan::updateOrCreate(['user_id' => $user->id], ['content' => $content]);
    }
}
