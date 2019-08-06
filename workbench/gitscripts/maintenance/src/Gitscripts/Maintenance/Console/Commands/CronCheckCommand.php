<?php namespace Gitscripts\Maintenance\Console\Commands;


use Illuminate\Database\Console\Migrations\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Gitscripts\Maintenance\ConsoleColors;
use Gitscripts\Maintenance\DatabaseBuilder;

class CronCheckCommand extends BaseCommand
{
    protected $name = 'cron:check';
    protected $description = 'Log that the cron is running';
    protected $filePath;
    protected $fileName;

    public function fire()
    {
        $today = new \DateTime('now');
        \Log::info('Cron Is Running '. $today->format('Y-m-d H:i:s') );
    }

    protected function getArguments()
    {
        return array();
    }

    protected function getOptions()
    {
        return array();
    }

    protected function checkDumpFolder()
    {

    }
}