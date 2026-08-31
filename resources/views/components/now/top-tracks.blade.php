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

    <p class="mr-auto text-xs tracking-wider uppercase text-muted">Top tracks</p>

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
                    <li class="flex items-baseline gap-3 px-2 py-1 -mx-2 transition-colors rounded group hover:bg-edge/50">
                        <span class="text-sm tabular-nums text-muted">{{ $loop->iteration }}</span>
                        <span class="min-w-0 grow">
                            <a href="{{ $track['url'] }}" rel="noopener" target="_blank" class="block text-sm truncate t-link">{{ $track['name'] }}</a>
                            <span class="block text-xs truncate text-muted">{{ $track['artist'] }}</span>
                        </span>
                        @if($track['plays'])
                            <span class="text-xs text-muted shrink-0">{{ $track['plays'] }} plays</span>
                        @endif
                        {{-- `??` because a chart cached before this key existed
                             outlives the deploy that added it. --}}
                        @if($track['play'] ?? null)
                            {{-- Hidden until the row is hovered, but a pointer
                                 that cannot hover never would: keep it in view
                                 on touch, and on focus for the keyboard. --}}
                            <a href="{{ $track['play'] }}" rel="noopener" target="_blank"
                                aria-label="Play {{ $track['name'] }} on Spotify"
                                class="self-center transition-opacity opacity-0 text-term shrink-0 hover:text-fg group-hover:opacity-100 focus-visible:opacity-100 pointer-coarse:opacity-100">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-5">
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M10.2 8.6 16 12l-5.8 3.4V8.6Z" fill="currentColor" stroke="none" />
                                </svg>
                            </a>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endforeach
    @endforeach
</div>
