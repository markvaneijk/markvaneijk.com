@if($nowPlaying?->item && $nowPlaying?->item?->type == 'track')
    <a href="{{ $nowPlaying->item->external_urls->spotify }}" target="_blank" class="block bg-[#24d44e] p-4 rounded text-black">
        <img src="{{ $nowPlaying->item->album->images[0]->url }}" alt="{{ $nowPlaying->item->name }} - {{ $nowPlaying->item?->album?->name }}">
        <span class="block mt-4">
            <strong>{{ $nowPlaying->item->name }} ({{ $nowPlaying->item?->album?->name }})</strong><br>{{ collect($nowPlaying->item?->artists)->pluck('name')->implode(', ') }}</span>
    </a>
@endif
