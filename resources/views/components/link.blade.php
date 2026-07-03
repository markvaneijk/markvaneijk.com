<a href="{{ $href }}"
    @if($external)rel="noopener" target="_blank"@endif
    {{ $attributes->merge(['class' => 't-link']) }}>{{ $slot }}</a>
