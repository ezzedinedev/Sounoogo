<article class="group flex flex-col rounded-[1.75rem] bg-white p-5 ring-1 ring-line-2 shadow-sm">
    <div class="aspect-16/10 overflow-hidden rounded-2xl bg-surface">
        @if ($course->coverUrl())
            <img src="{{ $course->coverUrl() }}" alt="" loading="lazy" class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.03] motion-reduce:transition-none">
        @endif
    </div>
    <div class="flex flex-1 flex-col px-2 pt-5 pb-2">
        <p class="inline-flex items-center gap-x-2 text-xs font-medium uppercase tracking-wide text-primary-700">
            <x-icon name="book" :size="16" />{{ \App\Domain\Learning\Models\Course::LEVELS[$course->level] ?? $course->level }} · {{ $course->lessons_count }} leçons
        </p>
        <h3 class="mt-3 text-lg font-medium">{{ $course->title }}</h3>
        <p class="mt-2 text-[15px] text-muted-foreground-1 leading-relaxed flex-1">{{ $course->summary }}</p>
        <p class="mt-6 text-sm">
            @if ($course->isFree())
                <span class="font-medium text-primary-700">Offerte</span>
            @else
                <span class="font-medium">{{ number_format($course->price, 0, ',', ' ') }} F</span> <span class="text-muted-foreground">ou incluse dans l'abonnement</span>
            @endif
        </p>
    </div>
</article>
