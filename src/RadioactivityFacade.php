<?php

namespace Eighteen73\Radioactivity;

use Illuminate\Support\Facades\Facade;

class RadioactivityFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Radioactivity';
    }
}
