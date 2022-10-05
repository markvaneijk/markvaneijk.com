@if($nowPlaying?->item && $nowPlaying?->item?->type == 'track')
    <a href="{{ $nowPlaying->item->external_urls->spotify }}" target="_blank" class="hover:brightness-[1.15] shadow-lg transition hover:shadow-2xl ease-in-out duration-[1000ms] block bg-[#24d44e] p-4 rounded text-black">
        <img src="{{ $nowPlaying->item->album->images[0]->url }}" alt="{{ $nowPlaying->item->name }} - {{ $nowPlaying->item?->album?->name }}">
        <span class="block mt-4">
            <strong>{{ $nowPlaying->item->name }} ({{ $nowPlaying->item?->album?->name }})</strong><br>{{ collect($nowPlaying->item?->artists)->pluck('name')->implode(', ') }}</span>
    </a>
@endif
