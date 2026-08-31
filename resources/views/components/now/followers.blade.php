{{-- The mark says which followers these are, so the label doesn't repeat it. --}}
<x-now.stat href="https://x.com/{{ config('services.x.username') }}"
    label="Followers"
    :value="number_format($followers)"
    wide>
    <x-slot:logo><x-logo.x class="size-3.5" /></x-slot:logo>
</x-now.stat>
