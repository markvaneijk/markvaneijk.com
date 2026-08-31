<a href="{{ $push['url'] }}" rel="noopener" target="_blank"
    class="flex items-center gap-4 p-4 transition-colors border rounded-md sm:col-span-2 border-edge bg-panel hover:border-flame">
    <span class="min-w-0">
        <span class="block text-xs tracking-wider uppercase text-muted">
            <span class="text-term">●</span> Last push · {{ $push['pushed_at']->diffForHumans() }}
        </span>
        <span class="block mt-1 font-bold truncate">{{ $push['repo'] }}</span>
        @if($push['message'])
            <span class="block text-sm truncate text-muted">{{ $push['message'] }}</span>
        @endif
    </span>
    {{-- Sits where the music card puts its source mark, so the two wide cards
         line up. --}}
    <x-logo.github class="ml-auto size-5 shrink-0" />
</a>
