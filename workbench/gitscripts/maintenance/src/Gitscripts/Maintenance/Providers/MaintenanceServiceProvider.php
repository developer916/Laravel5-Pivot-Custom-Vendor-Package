<?php namespace Gitscripts\Maintenance\Providers;

use Illuminate\Support\ServiceProvider;
use Gitscripts\Maintenance\Console\Commands\BackupCommand;
use Gitscripts\Maintenance\Console\Commands\CronCheckCommand;
use Gitscripts\Maintenance\DatabaseBuilder;

class MaintenanceServiceProvider extends ServiceProvider
{

    protected $commands = [
      'Gitscripts\Maintenance\Console\Commands\BackupCommand',
        'Gitscripts\Maintenance\Console\Commands\CronCheckCommand'
    ];


    protected $defer = false;

    public function boot()
    {
        $packageDir = realpath(__DIR__.'/..');
    }

    public function register()
    {
        $this->registerCommands();

    }

    public function registerCommands()
    {
        $this->registerDbBackupCommand();
        $this->registerCronCheckCommand();
    }

    public function registerDbBackupCommand()
    {
        $this->app->singleton('command.db.backup',function($app){

            $dbBuilder = new DatabaseBuilder();

            return new BackupCommand($dbBuilder);
        });

        $this->commands('command.db.backup');
    }

    public function registerCronCheckCommand()
    {
        $this->app->singleton('command.cron.check',function($app){
            return new CronCheckCommand();
        });
        $this->commands('command.cron.check');
    }




    public function provides()
    {
        return[
            'command.db.backup',
            'command.cron.check'
        ];

    }



}