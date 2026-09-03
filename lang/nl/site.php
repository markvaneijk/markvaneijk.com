<?php

/**
 * Elk woord dat deze site zegt, in de taal waarin markvaneijk.nl wordt
 * geserveerd. lang/en/site.php houdt dezelfde sleutels voor markvaneijk.com;
 * wat hier ontbreekt, valt daarop terug.
 *
 * Een paar teksten dragen hun eigen markup, omdat de link midden in een zin
 * staat en geen twee talen hem op dezelfde plek zetten. Die worden met {!! !!}
 * afgedrukt — ze staan in dit bestand, niet in wat een bezoeker instuurt.
 */
return [
    'numbers' => [
        'decimal' => ',',
        'thousands' => '.',
    ],

    'meta' => [
        'tagline' => 'Laravel developer uit Nijmegen',
        'description' => 'Mark van Eijk is een full-stack Laravel-developer en ondernemer uit Nijmegen. Hij werkt met Laravel, React, Inertia en Tailwind CSS.',
    ],

    'theme' => [
        'toggle' => 'Thema wisselen',
        'to_light' => 'Schakel naar het lichte thema',
        'to_dark' => 'Schakel naar het donkere thema',
    ],

    'footer' => [
        'made_in' => 'Gemaakt in Nijmegen — de oudste stad van Nederland, sinds 98 n.Chr.',
        'open_source' => 'Deze website is <a href="https://github.com/markvaneijk/markvaneijk.com" rel="noopener" target="_blank" class="t-link">open source</a>.',
    ],

    'home' => [
        'description' => 'Hoi, ik ben Mark van Eijk — full-stack Laravel-developer en ondernemer uit Nijmegen. Ik bouw met Laravel, React, Inertia en Tailwind CSS.',
        'tagline' => 'Full-stack Laravel-developer & ondernemer uit Nijmegen.',
        'about_heading' => 'Over Mark van Eijk',
        'about' => [
            'stack' => 'Mark van Eijk is een full-stack Laravel-developer en ondernemer uit Nijmegen. Hij bouwt al meer dan tien jaar voor het web en werkt over de volle breedte van de stack: stevige back-ends in <a href="https://laravel.com" rel="noopener" target="_blank" class="t-link">Laravel</a> en PHP, interactieve front-ends met React, Inertia.js en Tailwind CSS, en de infrastructuur die het geheel draaiende houdt.',
            'work' => 'Mark werkt bij <a href="https://ux.nl" rel="noopener" target="_blank" class="t-link">UX</a> en bouwt op dit moment aan <a href="https://rocketee.rs" rel="noopener" target="_blank" class="t-link">Rocketeers</a>. Hij geeft om developer experience, performance en eenvoudige oplossingen voor ingewikkelde problemen, en deelt zo veel mogelijk van zijn werk als open source op <a href="https://github.com/markvaneijk" rel="noopener" target="_blank" class="t-link">GitHub</a> — waaronder deze website.',
            'writing' => 'Over Laravel, PHP en modern webdevelopment schrijft hij in zijn <a href=":posts" class="t-link">berichten</a>, en hij is beschikbaar voor nieuwe projecten. Hij is het snelst per e-mail te bereiken, of via X en LinkedIn.',
        ],
        'about_command' => [
            'developer' => 'Developer',
            'location' => 'Locatie',
            'role' => 'Rol',
            'company' => 'Bedrijf',
            'building' => 'Bouwt aan',
            'stack' => 'Stack',
            'writing' => 'Schrijft',
            'status' => 'Status',
        ],
        'role' => 'Full-stack webspinner',
        'available' => 'BESCHIKBAAR VOOR PROJECTEN',
        'email' => 'E-mail',
    ],

    'projects' => [
        'heading' => 'Projecten',
        'ux' => 'Het digital agency van Mark in Nijmegen — software bouwen met AI.',
        'backstage' => 'Open source packages voor Laravel en premium plugins voor Filament.',
        'teamflow' => 'Waarmee teams meten hoe ze samenwerken, en het stap voor stap beter maken.',
        'rocketeers' => 'Servers en sites beheren bij elke cloudprovider — self-hosting zonder gedoe.',
        'zorgformulieren' => 'Zorgformulieren digitaal afhandelen, voor zorgverleners met een BIG-nummer.',
        'artsenverklaringen' => 'Alle artsenverklaringen van Zorgverzekeraars Nederland, dagelijks bijgewerkt in één overzicht.',
        'piggie' => 'Budgetteren met een directe koppeling op de Bunq API.',
    ],

    'posts' => [
        'title' => 'Berichten',
        'description' => 'Berichten van Mark van Eijk over Laravel, PHP, React, Inertia en Tailwind CSS.',
        'empty' => 'Nog geen berichten gepubliceerd — de eerste over Laravel, React en sleutelen zijn in de maak.',
        'empty_meanwhile' => 'Kijk ondertussen <a href=":now" class="t-link">waar ik nu mee bezig ben</a>.',
        'published' => 'gepubliceerd',
        'updated' => 'bijgewerkt',
    ],

    'now' => [
        'title' => 'Nu',
        'description' => 'Waar Mark van Eijk op dit moment mee bezig is — wat hij luistert, wat hij bouwt en wat hij loopt.',
        'now_playing' => 'Speelt nu',
        'last_played' => 'Laatst gespeeld',
        'last_push' => 'Laatste push',
        'followers' => 'Volgers',
        'stars' => 'Sterren',
        'top_tracks' => 'Topnummers',
        'last_four_weeks' => 'Laatste 4 weken',
        'all_time' => 'Aller tijden',
        'play_on_spotify' => 'Speel :track af op Spotify',
        'plays' => ':count keer gespeeld',
        'distance_last_days' => 'Afstand · laatste :days dagen',
        'distance_last_months' => 'Afstand · laatste :months maanden',
        'commits_last_days' => 'Commits · laatste :days dagen',
        'pull_requests_merged' => '{1} pull request gemerged|[2,*] pull requests gemerged',
    ],

    'aliases' => [
        'title' => 'Mijn ZSH-aliassen',
        'description' => 'De ZSH-aliassen waarmee Mark van Eijk snel werkt in de macOS-terminal.',
        'intro' => 'In al die jaren heb ik een hoop aliassen verzameld om snel te kunnen werken in de macOS-terminal. Omdat ik zelf altijd nieuwsgierig ben naar wat anderen bedacht hebben, deel ik hier mijn huidige lijst.',
    ],

    'uses' => [
        'title' => 'Uses',
    ],

    'feed' => [
        'description' => 'Berichten van Mark van Eijk over Laravel, PHP, React, Inertia en Tailwind CSS.',
    ],

    'schema' => [
        'job_title' => 'Laravel-developer',
        'description' => 'Full-stack Laravel-developer en ondernemer uit Nijmegen.',
    ],
];
