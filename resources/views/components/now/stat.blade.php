@props(['label', 'value', 'unit' => null, 'href', 'wide' => false, 'as' => 'a'])

{{-- A card is one big link by default. Pass as="div" when it holds links of
     its own — an anchor cannot contain another one — and the number carries
     the link instead of the card. --}}
@php($linked = $as === 'a')

<{{ $as }} @if($linked) href="{{ $href }}" rel="noopener" target="_blank" @endif
    @class([
        'block p-5 transition-colors border rounded-md border-edge bg-panel',
        'hover:border-flame' => $linked,
        'sm:col-span-2' => $wide,
    ])>
    {{-- Two lines' worth of room whether the label wraps or not, so the numbers
         below stay on one line across the row of cards. --}}
    <p class="flex min-h-8 items-center gap-2 text-xs tracking-wider uppercase text-muted">
        @isset($logo){{ $logo }}@endisset
        {{ $label }}
    </p>
    {{-- The unit is repeated rather than shared: a newline between the number
         and it would render as a space, and `ml-1` is the whole gap. --}}
    <p class="mt-2 leading-none font-display">
        @if($linked)
            <span class="text-4xl font-bold">{{ $value }}</span>@if($unit)<span class="ml-1 text-lg text-muted">{{ $unit }}</span>@endif
        @else
            <a href="{{ $href }}" rel="noopener" target="_blank"
                class="text-4xl font-bold transition-colors hover:text-flame">{{ $value }}</a>@if($unit)<span class="ml-1 text-lg text-muted">{{ $unit }}</span>@endif
        @endif
    </p>
    {{ $slot }}
</{{ $as }}>
