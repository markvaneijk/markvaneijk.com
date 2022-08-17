<?php

namespace App\Http\Controllers;

class NowController
{
    public function __invoke()
    {
        return view('pages.now');
    }
}
