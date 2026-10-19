@props(['title', 'pretitle' => null, 'actions' => null])
<x-backoffice.layout :title="$title">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    @if ($pretitle)<div class="page-pretitle">{{ $pretitle }}</div>@endif
                    <h1 class="page-title">{{ $title }}</h1>
                </div>
                @if ($actions)
                    <div class="col-auto ms-auto d-print-none"><div class="btn-list">{{ $actions }}</div></div>
                @endif
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <x-backoffice.flash />
            {{ $slot }}
        </div>
    </div>
</x-backoffice.layout>
