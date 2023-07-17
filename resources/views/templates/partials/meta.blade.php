@php($title = isset(app()->view->getSections()['title']) ? app()->view->getSections()['title'].' - '.config('app.name') : config('app.name').' - Laravel Developer from Nijmegen, Netherlands')

<meta charset="utf-8">
<title>{{ $title }}</title>
<link rel="canonical" href="{{ url()->current() }}">
@if(isset(app()->view->getSections()['description']))
<meta name="description" content="{{ app()->view->getSections()['description'] }}">
@endif
@if(isset(app()->view->getSections()['keywords']))
<meta name="keywords" content="{{ app()->view->getSections()['keywords'] }}">
@endif
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="apple-mobile-web-app-title" content="Mark van Eijk">
<meta property="author" content="Mark van Eijk">
<meta name="generator" content="Rocketeers">
<meta name="robots" content="index,follow">
