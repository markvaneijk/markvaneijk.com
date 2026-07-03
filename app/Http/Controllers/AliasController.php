<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class AliasController
{
    public function __invoke()
    {
        $gist = trim(Http::get('https://gist.githubusercontent.com/markvaneijk/7e6b248506295554146df757a020e076/raw?v='.time())->body());

        return view('pages.aliases', compact('gist'));
    }
}
