<?php

namespace App\Http\Controllers;

class AliasController
{
    public function __invoke()
    {
        $gist = trim(file_get_contents('https://gist.githubusercontent.com/markvaneijk/7e6b248506295554146df757a020e076/raw?v='.time()));

        return view('pages.aliases', compact('gist'));
    }
}
