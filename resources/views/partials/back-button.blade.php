@php
    $homeUrl = filament()->getHomeUrl() ?? '/admin';
    $currentUrl = rtrim(url()->current(), '/');
    $isHome = $currentUrl === rtrim(url($homeUrl), '/');
@endphp

@if (! $isHome)
    <div style="margin-inline-start: 0.75rem;">
        <x-filament::button
            color="gray"
            size="sm"
            icon="heroicon-o-arrow-left"
            labeled-from="sm"
            x-on:click="(window.history.length > 1 && document.referrer && document.referrer.startsWith(window.location.origin)) ? window.history.back() : window.location.href = '{{ $homeUrl }}'"
        >
            Volver
        </x-filament::button>
    </div>
@endif
