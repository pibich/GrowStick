<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Snapshot directory
    |--------------------------------------------------------------------------
    |
    | Non-private notes are written here as immutable, versioned JSON files
    | so public and unlisted notes stay readable even when the database is
    | unavailable. Defaults to public/snapshots.
    |
    */

    'snapshots_path' => env('NOTES_SNAPSHOTS_PATH', public_path('snapshots')),
];
