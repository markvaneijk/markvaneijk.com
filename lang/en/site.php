<?php

/**
 * Every word this site says, in the language markvaneijk.com is served in.
 * lang/nl/site.php holds the same keys for markvaneijk.nl; anything missing
 * there falls back to what stands here.
 *
 * A few strings carry their own markup, because the link sits in the middle of
 * a sentence and no two languages put it in the same place. Those are printed
 * with {!! !!} — they are written in this file, not by a visitor.
 */
return [
    /**
     * How this language writes a number: 1,234 and 15.3 here, 1.234 and 15,3
     * in Dutch. Read by App\Support\Number.
     */
    'numbers' => [
        'decimal' => '.',
        'thousands' => ',',
    ],

    'meta' => [
        'tagline' => 'Laravel Developer from Nijmegen, Netherlands',
        'description' => 'Mark van Eijk is a full-stack Laravel developer and entrepreneur from Nijmegen, the Netherlands, working with Laravel, React, Inertia and Tailwind CSS.',
    ],

    'theme' => [
        'toggle' => 'Toggle theme',
        'to_light' => 'Switch to light theme',
        'to_dark' => 'Switch to dark theme',
    ],

    'footer' => [
        'made_in' => 'Made in Nijmegen — the oldest city in the Netherlands, est. 98 AD.',
        'open_source' => 'This website is <a href="https://github.com/markvaneijk/markvaneijk.com" rel="noopener" target="_blank" class="t-link">open source</a>.',
    ],

    'home' => [
        'description' => "Hi, I'm Mark van Eijk — a full-stack Laravel developer and entrepreneur from Nijmegen, the Netherlands. I build with Laravel, React, Inertia and Tailwind CSS.",
        'tagline' => 'Full-stack Laravel developer & entrepreneur from Nijmegen, the Netherlands.',
        'about_heading' => 'About Mark van Eijk',
        'about' => [
            'stack' => 'Mark van Eijk is a full-stack Laravel developer and entrepreneur from Nijmegen, the Netherlands. He has been building for the web for more than a decade and works across the entire stack: solid back ends in <a href="https://laravel.com" rel="noopener" target="_blank" class="t-link">Laravel</a> and PHP, interactive front ends with React, Inertia.js and Tailwind CSS, and the infrastructure that keeps it all running.',
            'work' => 'Mark works at <a href="https://ux.nl" rel="noopener" target="_blank" class="t-link">UX</a> and is currently building <a href="https://rocketee.rs" rel="noopener" target="_blank" class="t-link">Rocketeers</a>. He cares about developer experience, performance and simple solutions to complicated problems, and shares as much of his work as possible as open source on <a href="https://github.com/markvaneijk" rel="noopener" target="_blank" class="t-link">GitHub</a> — including this website.',
            'writing' => 'He writes about Laravel, PHP and modern web development in his <a href=":posts" class="t-link">posts</a>, and he is available for new projects. The fastest way to reach him is by e-mail, or through X and LinkedIn.',
        ],
        /** The left-hand column of `php artisan about`. */
        'about_command' => [
            'developer' => 'Developer',
            'location' => 'Location',
            'role' => 'Role',
            'company' => 'Company',
            'building' => 'Building',
            'stack' => 'Stack',
            'writing' => 'Writing',
            'status' => 'Status',
        ],
        'role' => 'Full-Stack Maker of Webs',
        'available' => 'AVAILABLE FOR PROJECTS',
        'email' => 'E-mail',
    ],

    /** The one line under each project name in `ls ~/projects` on the home page. */
    'projects' => [
        'heading' => 'Projects',
        'ux' => "Mark's digital agency in Nijmegen — building software with AI.",
        'backstage' => 'Open source packages for Laravel and premium plugins for Filament.',
        'teamflow' => 'Where teams measure how they work together, and improve it step by step.',
        'rocketeers' => 'Manage servers and sites at any cloud provider — self-hosting made effortless.',
        'zorgformulieren' => 'Dutch healthcare forms, handled digitally by registered care providers.',
        'artsenverklaringen' => 'Every Dutch medical declaration form from Zorgverzekeraars Nederland, updated daily in one overview.',
        'piggie' => 'Budgeting with a direct connection to the Bunq API.',
    ],

    'posts' => [
        'title' => 'Posts',
        'description' => 'Posts by Mark van Eijk on Laravel, PHP, React, Inertia and Tailwind CSS.',
        'empty' => 'No posts published yet — the first ones about Laravel, React and tinkering are in the works.',
        'empty_meanwhile' => 'In the meantime, check out <a href=":now" class="t-link">what I\'m doing now</a>.',
        'published' => 'published',
        'updated' => 'updated',
    ],

    'now' => [
        'title' => 'Now',
        'description' => "What Mark van Eijk is up to right now — what he's listening to, building and running.",
        'now_playing' => 'Now playing',
        'last_played' => 'Last played',
        'last_push' => 'Last push',
        'followers' => 'Followers',
        'stars' => 'Stars',
        'top_tracks' => 'Top tracks',
        'last_four_weeks' => 'Last 4 weeks',
        'all_time' => 'All time',
        'play_on_spotify' => 'Play :track on Spotify',
        'plays' => ':count plays',
        'distance_last_days' => 'Distance · last :days days',
        'distance_last_months' => 'Distance · last :months months',
        'commits_last_days' => 'Commits · last :days days',
        'pull_requests_merged' => '{1} pull request merged|[2,*] pull requests merged',
    ],

    'aliases' => [
        'title' => 'My ZSH aliases',
        'description' => 'The ZSH aliases Mark van Eijk uses to move quickly in the macOS terminal.',
        'intro' => "In all these years I've collected a lot of aliases to move quickly in the macOS terminal. Because I always find it interesting what others came up with, I share here my current list of aliases.",
    ],

    'uses' => [
        'title' => 'Uses',
    ],

    'feed' => [
        'description' => 'Posts by Mark van Eijk on Laravel, PHP, React, Inertia and Tailwind CSS.',
    ],

    /** The JSON-LD in the head, which search engines read in this language too. */
    'schema' => [
        'job_title' => 'Laravel Developer',
        'description' => 'Full-stack Laravel developer and entrepreneur from Nijmegen, the Netherlands.',
    ],
];
