@props(['label', 'value', 'unit' => null, 'href', 'wide' => false])

<a href="{{ $href }}" rel="noopener" target="_blank"
    @class([
        'block p-5 transition-colors border rounded-md border-edge bg-panel hover:border-flame',
        'sm:col-span-2' => $wide,
    ])>
    {{-- Two lines' worth of room whether the label wraps or not, so the numbers
         below stay on one line across the row of cards. --}}
    <p class="flex min-h-8 items-center gap-2 text-xs tracking-wider uppercase text-muted">
        @isset($logo){{ $logo }}@endisset
        {{ $label }}
    </p>
    <p class="mt-2 leading-none font-display">
        <span class="text-4xl font-bold">{{ $value }}</span>@if($unit)<span class="ml-1 text-lg text-muted">{{ $unit }}</span>@endif
    </p>
    {{ $slot }}
</a>
