<a href="{{ $href }}"
    @if($external)rel="noopener" target="_blank"@endif
    {{ $attributes->merge(['class' => 'underline underline-offset-4 decoration-4 decoration-yellow-400 hover:bg-yellow-100']) }}>{{ $slot }}</a>
