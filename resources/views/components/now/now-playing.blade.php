<a href="{{ $track['url'] }}" rel="noopener" target="_blank"
    class="flex items-center gap-4 p-4 transition-colors border rounded-md sm:col-span-2 border-edge bg-panel hover:border-flame">
    @if($track['image'])
        <img src="{{ $track['image'] }}" alt="" width="56" height="56" loading="lazy"
            class="rounded size-14 shrink-0">
    @endif
    <span class="min-w-0">
        <span class="block text-xs tracking-wider uppercase text-muted">
            @if($track['playing'])
                <span class="text-term pulse">●</span> {{ __('site.now.now_playing') }}
            @else
                ● {{ __('site.now.last_played') }}
                @if($track['played_at'])
                    · {{ $track['played_at']->diffForHumans() }}
                @endif
            @endif
        </span>
        <span class="block mt-1 font-bold truncate">{{ $track['name'] }}</span>
        <span class="block text-sm truncate text-muted">{{ $track['artist'] }}</span>
    </span>
    {{-- Names whichever service answered: Spotify, or Last.fm on the fallback. --}}
    <x-dynamic-component :component="'logo.'.$source" class="ml-auto size-5 shrink-0" />
</a>
