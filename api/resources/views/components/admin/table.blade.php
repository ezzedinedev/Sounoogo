{{-- Tableau de données : styles appliqués aux descendants pour garder des vues lisibles. --}}
<div {{ $attributes->merge(['class' => 'overflow-x-auto']) }}>
    <table class="w-full caption-bottom text-sm
        [&_thead_tr]:border-b [&_th]:h-10 [&_th]:px-4 [&_th]:text-start [&_th]:align-middle [&_th]:font-medium [&_th]:text-muted-foreground [&_th]:whitespace-nowrap [&_thead]:bg-muted/50
        [&_tbody_tr]:border-b [&_tbody_tr:last-child]:border-0 [&_tbody_tr]:transition-colors [&_tbody_tr:hover]:bg-muted/40
        [&_td]:px-4 [&_td]:py-3 [&_td]:align-middle">
        {{ $slot }}
    </table>
</div>
