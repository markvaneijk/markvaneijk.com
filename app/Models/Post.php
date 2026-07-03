<?php

namespace App\Models;

use Backstage\Static\Laravel\Facades\StaticCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Orbit\Concerns\Orbital;

class Post extends Model
{
    use Orbital;

    public $incrementing = false;

    protected static function booted()
    {
        // Note: only fires for changes made through the model (not for
        // markdown files added to content/posts directly) — run
        // `static:build` on deploy to cover those.
        $clear = fn (Post $post) => StaticCache::clear([
            '/'.$post->slug,
            '/posts',
            '/',
        ]);

        static::saved($clear);
        static::deleted($clear);
    }

    protected $casts = [
        'published_at' => 'date:Y-m-d',
        'updated_at' => 'date:Y-m-d',
    ];

    public function getKeyName()
    {
        return 'slug';
    }

    public static function schema(Blueprint $table)
    {
        $table->string('title');
        $table->string('slug');
        $table->text('content')->nullable();
        $table->date('published_at')->nullable();
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function getUrlAttribute()
    {
        return route('post', $this->attributes['slug']);
    }

    public function getExcerptAttribute()
    {
        return Str::limit(Str::squish(strip_tags(Str::markdown($this->content ?? ''))), 160);
    }
}
