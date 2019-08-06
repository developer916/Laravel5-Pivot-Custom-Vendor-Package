<?php namespace Pivotal\Admin;

use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->package('pivotal/admin','admin');
        include __DIR__ . '/../../routes.php';
    }

    public function register()
    {

    }

    public function provides()
    {

    }



}