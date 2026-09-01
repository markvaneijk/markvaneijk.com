@use('App\Support\Number')

<x-now.stat :href="$profileUrl"
    :label="$label"
    :value="Number::format($total)">
    <x-slot:logo><x-logo.github class="size-3.5" /></x-slot:logo>

    {{-- One bar per day, oldest on the left. Decoration for the number above
         it, so it stays out of the accessibility tree. --}}
    <span class="flex gap-px mt-4" aria-hidden="true">
        @foreach($levels as $level)
            <span class="flex-1 h-5 rounded-xs {{ ['bg-edge', 'bg-term/30', 'bg-term/55', 'bg-term/80', 'bg-term'][$level] }}"></span>
        @endforeach
    </span>

    {{-- Nothing merged is a quiet month, not a broken widget: drop the line
         rather than print a zero under the strip. --}}
    @if($merged)
        <span class="block mt-3 text-xs text-muted">
            <span class="text-term">{{ Number::format($merged) }}</span>
            {{ trans_choice('site.now.pull_requests_merged', $merged) }}
        </span>
    @endif
</x-now.stat>
