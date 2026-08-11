<a href="{{ $track['url'] }}" rel="noopener" target="_blank"
    class="flex items-center gap-4 p-4 transition-colors border rounded-md sm:col-span-2 border-edge bg-panel hover:border-flame">
    @if($track['image'])
        <img src="{{ $track['image'] }}" alt="" width="56" height="56" loading="lazy"
            class="rounded size-14 shrink-0">
    @endif
    <span class="min-w-0">
        <span class="block text-xs tracking-wider uppercase text-muted">
            @if($track['playing'])
                <span class="text-term pulse">●</span> Now playing
            @else
                ● Last played
                @if($track['played_at'])
                    · {{ $track['played_at']->diffForHumans() }}
                @endif
            @endif
        </span>
        <span class="block mt-1 font-bold truncate">{{ $track['name'] }}</span>
        <span class="block text-sm truncate text-muted">{{ $track['artist'] }}</span>
    </span>
</a>
