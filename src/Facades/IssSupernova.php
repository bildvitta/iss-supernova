<?php

namespace Bildvitta\IssSupernova\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Bildvitta\IssSupernova\IssSupernova
 */
class IssSupernova extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'iss-supernova';
    }
}
