@php
    /* Two radio groups and no JavaScript: one picks the service, one picks the
       window, and a list shows when both of its `peer-checked` variants hold.
       Every class is spelled out per service and window because Tailwind only
       compiles names it can read literally in the source — one built at
       runtime never lands in the stylesheet. */
    $tab = 'pb-1 text-xs tracking-wider uppercase transition-colors border-b-2 border-transparent cursor-pointer text-muted hover:text-fg';

    $services = [
        'spotify' => [
            'input' => 'peer/spotify',
            'tab' => 'peer-checked/spotify:border-flame peer-checked/spotify:text-fg peer-focus-visible/spotify:outline-2 peer-focus-visible/spotify:outline-term peer-focus-visible/spotify:outline-offset-4',
            'lists' => [
                'peer-checked/spotify:peer-checked/recent:block',
                'peer-checked/spotify:peer-checked/lasting:block',
            ],
        ],
        'lastfm' => [
            'input' => 'peer/lastfm',
            'tab' => 'peer-checked/lastfm:border-flame peer-checked/lastfm:text-fg peer-focus-visible/lastfm:outline-2 peer-focus-visible/lastfm:outline-term peer-focus-visible/lastfm:outline-offset-4',
            'lists' => [
                'peer-checked/lastfm:peer-checked/recent:block',
                'peer-checked/lastfm:peer-checked/lasting:block',
            ],
        ],
    ];

    $windowTabs = [
        [
            'input' => 'peer/recent',
            'tab' => 'peer-checked/recent:border-flame peer-checked/recent:text-fg peer-focus-visible/recent:outline-2 peer-focus-visible/recent:outline-term peer-focus-visible/recent:outline-offset-4',
        ],
        [
            'input' => 'peer/lasting',
            'tab' => 'peer-checked/lasting:border-flame peer-checked/lasting:text-fg peer-focus-visible/lasting:outline-2 peer-focus-visible/lasting:outline-term peer-focus-visible/lasting:outline-offset-4',
        ],
    ];
@endphp

{{-- Everything sits in one wrapping row: `peer-checked` only reaches an
     element that shares a parent with the radio, so the tabs and the lists
     cannot be nested away into rows of their own. --}}
<div class="flex flex-wrap items-center p-5 border rounded-md sm:col-span-2 gap-x-4 border-edge bg-panel">
    @foreach($sources as $source)
        <input type="radio" name="top-tracks-service" id="top-tracks-{{ $source['key'] }}"
            class="sr-only {{ $services[$source['key']]['input'] }}" @checked($loop->first)>
    @endforeach

    @foreach($windows as $index => $window)
        <input type="radio" name="top-tracks-window" id="top-tracks-window-{{ $index }}"
            class="sr-only {{ $windowTabs[$index]['input'] }}" @checked($loop->first)>
    @endforeach

    <p class="mr-auto text-xs tracking-wider uppercase text-muted">{{ __('site.now.top_tracks') }}</p>

    {{-- Which service counted. A tab each while both answered, and a plain
         label when only one did: there is nothing to switch to then. --}}
    @foreach($sources as $source)
        @if(count($sources) > 1)
            <label for="top-tracks-{{ $source['key'] }}" class="flex items-center gap-2 {{ $tab }} {{ $services[$source['key']]['tab'] }}">
                <x-dynamic-component :component="'logo.'.$source['key']" class="size-3.5" />
                {{ $source['label'] }}
            </label>
        @else
            <span class="flex items-center gap-2 text-xs tracking-wider uppercase text-muted">
                <x-dynamic-component :component="'logo.'.$source['key']" class="size-3.5" />
                {{ $source['label'] }}
            </span>
        @endif
    @endforeach

    {{-- Forces the window tabs onto a line of their own; a nested row would
         put them out of reach of the radios above. --}}
    <span class="w-full"></span>

    @foreach($windows as $index => $window)
        <label for="top-tracks-window-{{ $index }}" class="mt-4 {{ $tab }} {{ $windowTabs[$index]['tab'] }}">
            {{ $window }}
        </label>
    @endforeach

    {{-- One list per service and window; the checked pair is the one shown. --}}
    @foreach($sources as $source)
        @foreach($source['charts'] as $index => $tracks)
            <ol class="hidden w-full mt-4 space-y-2 {{ $services[$source['key']]['lists'][$index] }}">
                @foreach($tracks as $track)
                    {{-- `edge` is the one border token that sits lighter than
                         the panel on the dark theme and darker on the light
                         one, so the same tint reads as a highlight in both. --}}
                    {{-- One link per row, stretched over the whole `li` below:
                         the title carries it, so the row keeps a single
                         accessible name and the rank, the artist and the play
                         count are all part of the same hit area. --}}
                    <li class="relative flex items-baseline gap-3 px-2 py-1 -mx-2 transition-colors rounded group hover:bg-edge/50">
                        @php
                            /* `??` because a chart cached before this key
                               existed outlives the deploy that added it. */
                            $play = $track['play'] ?? null;

                            /* The row opens the track the best way the service
                               allows: Spotify's `spotify:track:…`, which the
                               installed client answers, and the plain page for
                               everything else. A client URI never leaves the
                               browser, so it gets no tab of its own — and a
                               cached chart still holding the old https `play`
                               is served by the same test. */
                            $href = $play ?: $track['url'];
                            $opensATab = ! str_starts_with($href, 'spotify:');
                        @endphp

                        {{-- Rank and play icon share one fixed-width slot: the
                             number gives way to the icon on hover, so the rows
                             below never shift as the pointer travels down. --}}
                        <span class="relative w-5 text-sm text-right tabular-nums shrink-0 text-muted">
                            <span @class([
                                'transition-opacity',
                                'group-hover:opacity-0 group-focus-within:opacity-0 pointer-coarse:opacity-0' => $play,
                            ])>{{ $loop->iteration }}</span>

                            @if($play)
                                {{-- Decoration, not a second link: the row itself
                                     already opens the track. Hidden until hover,
                                     but a pointer that cannot hover never would —
                                     so it stays in view on touch, and on focus
                                     for the keyboard. --}}
                                <span aria-hidden="true"
                                    class="absolute inset-0 flex items-center justify-end transition-opacity opacity-0 pointer-events-none text-term group-hover:opacity-100 group-focus-within:opacity-100 pointer-coarse:opacity-100">
                                    {{-- Lucide `play`, filled so it still reads as
                                         a button at this size. --}}
                                    <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                                        <path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z" />
                                    </svg>
                                </span>
                            @endif
                        </span>
                        <span class="min-w-0 grow">
                            {{-- `truncate` sits on the inner span, never on the
                                 anchor: its `overflow: hidden` would clip the
                                 pseudo-element that does the stretching. --}}
                            <a href="{{ $href }}"
                                @if($opensATab) rel="noopener" target="_blank" @endif
                                @if($play) aria-label="{{ __('site.now.play_on_spotify', ['track' => $track['name']]) }}" @endif
                                class="block text-sm t-link after:absolute after:inset-0 after:rounded focus-visible:outline-none focus-visible:after:outline-2 focus-visible:after:outline-term focus-visible:after:outline-offset-2">
                                <span class="block truncate">{{ $track['name'] }}</span>
                            </a>
                            <span class="block text-xs truncate text-muted">{{ $track['artist'] }}</span>
                        </span>
                        @if($track['plays'])
                            <span class="text-xs text-muted shrink-0">{{ __('site.now.plays', ['count' => $track['plays']]) }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endforeach
    @endforeach
</div>
