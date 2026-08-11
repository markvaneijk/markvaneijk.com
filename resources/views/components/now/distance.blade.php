<x-now.stat href="https://www.strava.com/athletes/{{ config('services.strava.athlete_id') }}"
    label="Distance · last 30 days"
    :value="number_format($distance, 1)"
    unit="km" />
