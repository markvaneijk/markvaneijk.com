<x-now.stat href="https://x.com/{{ config('services.x.username') }}"
    label="Followers on X"
    :value="number_format($followers)" />
