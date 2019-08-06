<?php namespace Pivotal\User\Facades;

use Illuminate\Support\Facades\Facade;

class Teacher extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'teacher';
    }
}