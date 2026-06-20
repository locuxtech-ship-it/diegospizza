<x-filament-panels::page>
    {{ $this->content }}

    @push('styles')
    <style>
        .fi-ta-group-header th {
            background-color: #fef3c7 !important;
        }
        .fi-ta-group-header th:hover {
            background-color: #fde68a !important;
        }
    </style>
    @endpush
</x-filament-panels::page>
