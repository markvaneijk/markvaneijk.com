@use('App\Support\Number')

<x-now.stat href="https://www.strava.com/athletes/{{ config('services.strava.athlete_id') }}"
    :label="$label"
    :value="Number::format($distance, 1)"
    unit="km">
    <x-slot:logo><x-logo.strava class="size-3.5" /></x-slot:logo>
</x-now.stat>
