<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Decay Half Life (hours)
    |--------------------------------------------------------------------------
    |
    | Defines the half life of an element. This is the average time it would
    | take for an element's radioactivity to each 50% of it's initial
    | starting value.
    |
    */
    'half_life' => env('RADIOACTIVITY_HALF_LIFE', 24),

    /*
    |--------------------------------------------------------------------------
    | Minimum energy
    |--------------------------------------------------------------------------
    |
    | Defines the minimum energy a model can have before being pruned from the
    | energies table. This stops the energies table becoming enormously
    | massive when a lot of content is present, and keeps content relevant.
    |
    */
    'min_energy' => env('RADIOACTIVITY_MIN_ENERGY', 1),

    /*
    |--------------------------------------------------------------------------
    | IP Blacklist
    |--------------------------------------------------------------------------
    |
    | Sometimes you may want to prevent an IP to add energy to a model.
    | You can add as many IPs as you want to.
    |
    */
    'ip_blacklist' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Defines models here you would like to automatically decay on a schedule.
    | Every 5 minutes a job will be queued to simulate decay of energy
    | over time, based on the half life specified above.
    |
    */
    'models' => [
        // \App\Models\Post::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | The queue to dispatch the decay onto.
    |
    */
    'queue' => 'default',
];
