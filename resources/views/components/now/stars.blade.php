{{-- Not an <x-now.stat>: the packages under the number are links of their
     own, and an anchor cannot hold another one. --}}
<div class="p-5 border rounded-md border-edge bg-panel">
    <p class="text-xs tracking-wider uppercase text-muted">Stars on GitHub</p>
    <p class="mt-2 leading-none font-display">
        <a href="{{ $profileUrl }}" rel="noopener" target="_blank"
            class="text-4xl font-bold transition-colors hover:text-flame">{{ number_format($stars) }}</a>
    </p>

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
</div>
