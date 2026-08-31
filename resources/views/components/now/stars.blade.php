{{-- as="div": the packages below are links of their own. --}}
<x-now.stat as="div"
    :href="$profileUrl"
    label="Stars"
    :value="number_format($stars)">
    <x-slot:logo><x-logo.github class="size-3.5" /></x-slot:logo>

    @if($popular)
        <ul class="mt-4 space-y-2">
            @foreach($popular as $repo)
                <li class="flex items-baseline gap-3 text-xs">
                    <a href="{{ $repo['url'] }}" rel="noopener" target="_blank"
                        title="{{ $repo['full_name'] }}"
                        class="min-w-0 truncate grow t-link">{{ $repo['name'] }}</a>
                    <span class="text-muted shrink-0">{{ number_format($repo['stars']) }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</x-now.stat>
