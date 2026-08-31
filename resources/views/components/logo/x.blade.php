{{-- Brand mark from simple-icons. The width/height attributes are only a
     fallback: any size-* class the caller passes outranks them. Its brand
     colour is black, which would vanish on the dark panel, so it takes the
     foreground and flips with the theme — as X's own mark does. --}}
<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" role="img" aria-label="X"
    {{ $attributes->merge(['class' => 'text-fg']) }}>
    <path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.153h7.594l5.243 6.932zM17.61 20.644h2.039L6.486 3.24H4.298z" />
</svg>
