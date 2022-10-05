@if($post)
    <a href="https://instagram.com/{{ config('services.instagram.username') }}"><img src="{{ $post->image_url }}"></a>
@endif
