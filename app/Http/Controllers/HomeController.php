<?php

namespace App\Http\Controllers;

class HomeController
{
    public function __invoke()
    {
        return view('pages.home');
    }
}
