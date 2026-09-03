<?php

namespace App\Http\Controllers;

class ProjectsController
{
    public function __invoke()
    {
        return view('pages.projects');
    }
}
