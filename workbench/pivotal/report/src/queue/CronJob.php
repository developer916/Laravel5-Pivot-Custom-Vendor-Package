<?php namespace Pivotal\Report\Queue;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronJob
{
    protected $name = 'cron:run.report.queue';
    protected $timestamp;
    protected $messages = array();

    public function run()
    {
        \Log::debug('Pivotal\Report\Queue\CronJob->run()');

    }

    public function finish()
    {

    }

}