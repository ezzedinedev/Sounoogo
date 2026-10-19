<article class="group flex flex-col rounded-3xl bg-card border border-card-line overflow-hidden">
    <div class="aspect-16/10 bg-surface overflow-hidden">
        @if ($course->coverUrl())
            <img src="{{ $course->coverUrl() }}" alt="" loading="lazy" class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.03] motion-reduce:transition-none">
        @endif
    </div>
    <div class="flex flex-1 flex-col p-6">
        <div class="flex items-center gap-x-2 text-xs">
            <span class="rounded-full bg-secondary px-2.5 py-1 font-medium text-secondary-foreground">{{ \App\Domain\Learning\Models\Course::LEVELS[$course->level] ?? $course->level }}</span>
            <span class="text-muted-foreground">{{ $course->lessons_count }} leçons · {{ (int) $course->lessons_sum_duration_minutes }} min</span>
        </div>
        <h3 class="mt-3 text-lg font-semibold">{{ $course->title }}</h3>
        <p class="mt-2 text-sm text-muted-foreground-1 leading-relaxed flex-1">{{ $course->summary }}</p>
        <p class="mt-5 text-sm font-semibold">
            @if ($course->isFree())
                <span class="text-accent">Offerte</span>
            @else
                <x-site.price :amount="$course->price" /> <span class="font-normal text-muted-foreground">ou incluse dans l'abonnement</span>
            @endif
        </p>
    </div>
</article>
