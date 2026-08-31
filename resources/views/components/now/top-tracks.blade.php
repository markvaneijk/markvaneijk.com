<div class="p-5 border rounded-md sm:col-span-2 border-edge bg-panel">
    <p class="flex items-center gap-2 text-xs tracking-wider uppercase text-muted">
        {{-- Names whichever service answered: Spotify, or Last.fm on the fallback. --}}
        <x-dynamic-component :component="'logo.'.$source" class="size-3.5" />
        Top tracks · last 30 days
    </p>
    <ol class="mt-4 space-y-3">
        @foreach($tracks as $track)
            <li class="flex items-baseline gap-3">
                <span class="text-sm text-muted">{{ $loop->iteration }}</span>
                <span class="min-w-0 grow">
                    <a href="{{ $track['url'] }}" rel="noopener" target="_blank" class="block truncate t-link">{{ $track['name'] }}</a>
                    <span class="block text-xs truncate text-muted">{{ $track['artist'] }}</span>
                </span>
                @if($track['plays'])
                    <span class="text-xs text-muted shrink-0">{{ $track['plays'] }} plays</span>
                @endif
            </li>
        @endforeach
    </ol>
</div>
