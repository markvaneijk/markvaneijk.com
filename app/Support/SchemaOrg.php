<?php

namespace App\Support;

use App\Models\Post;

/**
 * Schema.org JSON-LD builders.
 *
 * These live in a plain PHP class because Blade compiles the literal token
 * "@context" into its @context directive — even inside PHP string literals —
 * corrupting any JSON-LD written directly in a .blade.php file.
 */
class SchemaOrg
{
    public static function person(): string
    {
        return static::encode([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            '@id' => url('/').'#mark',
            'name' => 'Mark van Eijk',
            'url' => url('/'),
            'image' => asset('images/mark-van-eijk.png'),
            'jobTitle' => 'Laravel Developer',
            'description' => 'Full-stack Laravel developer and entrepreneur from Nijmegen, the Netherlands.',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Nijmegen',
                'addressCountry' => 'NL',
            ],
            'worksFor' => [
                '@type' => 'Organization',
                'name' => 'UX',
                'url' => 'https://ux.nl',
            ],
            'knowsAbout' => ['Laravel', 'PHP', 'React', 'Inertia', 'Tailwind CSS'],
            'sameAs' => [
                'https://github.com/markvaneijk',
                'https://x.com/markvaneijk',
                'https://linkedin.com/in/markveijk',
            ],
        ]);
    }

    public static function blogPosting(Post $post): string
    {
        return static::encode([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->excerpt,
            'url' => $post->url,
            'datePublished' => $post->published_at->format('Y-m-d'),
            'dateModified' => ($post->updated_at ?? $post->published_at)->format('Y-m-d'),
            'author' => ['@id' => url('/').'#mark'],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $post->url,
            ],
        ]);
    }

    protected static function encode(array $schema): string
    {
        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
