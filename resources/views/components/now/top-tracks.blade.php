@php
    /* The tabs run on two radios and no JavaScript: whichever is checked shows
       its own list through the `peer-checked` variants below. Both sets are
       spelled out per chart because Tailwind only compiles class names it can
       read literally in the source — a name built at runtime never lands in
       the stylesheet. */
    $tabs = [
        [
            'input' => 'peer/recent',
            'label' => 'peer-checked/recent:border-flame peer-checked/recent:text-fg peer-focus-visible/recent:outline-2 peer-focus-visible/recent:outline-term peer-focus-visible/recent:outline-offset-4',
            'panel' => 'peer-checked/recent:block',
        ],
        [
            'input' => 'peer/lasting',
            'label' => 'peer-checked/lasting:border-flame peer-checked/lasting:text-fg peer-focus-visible/lasting:outline-2 peer-focus-visible/lasting:outline-term peer-focus-visible/lasting:outline-offset-4',
            'panel' => 'peer-checked/lasting:block',
        ],
    ];
@endphp

{{-- One grid rather than nested rows: `peer-checked` only reaches an element
     that shares a parent with the radio, so the labels and the lists have to
     be siblings of it, and the grid puts them back into rows. --}}
<div class="grid p-5 border rounded-md sm:col-span-2 grid-cols-[auto_auto_1fr] gap-x-6 border-edge bg-panel">
    @foreach($charts as $index => $chart)
        <input type="radio" name="top-tracks" id="top-tracks-{{ $index }}"
            class="sr-only {{ $tabs[$index]['input'] }}" @checked($loop->first)>
    @endforeach

    <p class="flex items-center col-span-3 gap-2 text-xs tracking-wider uppercase text-muted">
        {{-- Names whichever service answered: Spotify, or Last.fm on the fallback. --}}
        <x-dynamic-component :component="'logo.'.$source" class="size-3.5" />
        Top tracks
    </p>

    @foreach($charts as $index => $chart)
        <label for="top-tracks-{{ $index }}"
            class="pb-1 mt-4 text-xs tracking-wider uppercase transition-colors border-b-2 border-transparent cursor-pointer text-muted hover:text-fg {{ $tabs[$index]['label'] }}">
            {{ $chart['label'] }}
        </label>
    @endforeach

    @foreach($charts as $index => $chart)
        {{-- Fifty tracks read better in two columns than in one long scroll. --}}
        <ol class="hidden col-span-3 mt-4 sm:columns-2 sm:gap-6 {{ $tabs[$index]['panel'] }}">
            @foreach($chart['tracks'] as $track)
                <li class="flex items-baseline gap-3 mb-2 break-inside-avoid">
                    <span class="text-sm tabular-nums text-muted">{{ $loop->iteration }}</span>
                    <span class="min-w-0 grow">
                        <a href="{{ $track['url'] }}" rel="noopener" target="_blank" class="block text-sm truncate t-link">{{ $track['name'] }}</a>
                        <span class="block text-xs truncate text-muted">{{ $track['artist'] }}</span>
                    </span>
                    @if($track['plays'])
                        <span class="text-xs text-muted shrink-0">{{ $track['plays'] }} plays</span>
                    @endif
                </li>
            @endforeach
        </ol>
    @endforeach
</div>
