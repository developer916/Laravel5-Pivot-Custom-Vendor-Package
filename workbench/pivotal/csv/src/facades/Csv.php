<?php namespace Pivotal\Csv\Facades;

use Illuminate\Support\Facades\Facade;

class Csv extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'csv';
    }
}