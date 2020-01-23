<?php

namespace App;

use Spatie\Sheets\Sheet;

class Post extends Sheet
{
    public function getUrlAttribute(): string
    {
        return url($this->slug);
    }
}
