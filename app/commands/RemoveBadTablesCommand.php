<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RemoveBadTablesCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'limesurvey:tablefix';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Remove empty unused limesurvey tables';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$tables = DB::connection('limemysql')
				->getPdo()
				->query("SHOW FULL TABLES")
				->fetchAll();
		foreach ($tables as $name) {
			$tableName = $name[0];
			if (preg_match("/survey_[0-9]{5,7}$/i", $tableName)) {
				$count = DB::connection('limemysql')
						->getPdo()
						->query("SELECT COUNT(*) as `c` FROM ".$tableName)
						->fetch();
				if ($count['c'] == 0) {
					preg_match("/survey_([0-9]{5,7})/i", $tableName, $m);
					$surveyId = $m[1];
					$countSurvey = DB::connection('mysql')
							->getPdo()
							->query("SELECT COUNT(*) as `c` FROM `cycles_classes` WHERE `limesurvey_id` = '".$surveyId."'")
							->fetch();
					if ($countSurvey['c'] == 0) {
						DB::connection('limemysql')
								->getPdo()
								->prepare("DROP TABLE `".$tableName."`")
								->execute();
						DB::connection('limemysql')
								->getPdo()
								->prepare("DROP TABLE `".$tableName."_timings`")
								->execute();
					}
				}
			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
