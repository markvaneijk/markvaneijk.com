@props(['label', 'value', 'unit' => null, 'href'])

<a href="{{ $href }}" rel="noopener" target="_blank"
    class="block p-5 transition-colors border rounded-md border-edge bg-panel hover:border-flame">
    <p class="text-xs tracking-wider uppercase text-muted">{{ $label }}</p>
    <p class="mt-2 leading-none font-display">
        <span class="text-4xl font-bold">{{ $value }}</span>@if($unit)<span class="ml-1 text-lg text-muted">{{ $unit }}</span>@endif
    </p>
</a>
