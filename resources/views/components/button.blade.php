<a href="{{ $href }}"
    @if($external)rel="noopener" target="_blank"@endif
    {{ $attributes->merge(['class' => 'inline-block px-4 py-3 text-sm font-bold tracking-wider uppercase transition-all duration-150 border rounded-md border-edge bg-panel hover:border-flame hover:shadow-[0_0_24px_rgba(255,45,32,0.35)]']) }}>{{ $slot }}</a>
