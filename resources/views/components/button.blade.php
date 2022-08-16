<a href="{{ $href }}"
    @if($external)rel="noopener" target="_blank"@endif
    {{ $attributes->merge(['class' => 'shadow-sm px-4 py-2.5 inline-block bg-slate-100 hover:bg-slate-50 rounded-md hover:shadow-md transition-shadow duration-700']) }}>{{ $slot }}</a>
