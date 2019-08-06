<?php

class LimeBadTableRemoveTest extends TestCase {

	/**
	 * @group limebad
	 */
	public function testCreateTable()
	{
		$num = '9999999';

		DB::connection('limemysql')
				->getPdo()
				->prepare("CREATE TABLE IF NOT EXISTS `survey_".$num."` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `token` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;")
				->execute();

		DB::connection('limemysql')
				->getPdo()
				->prepare("CREATE TABLE IF NOT EXISTS `survey_".$num."_timings` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `token` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;")
				->execute();

		try {
			$result = DB::connection('limemysql')
					->getPdo()
					->query("SELECT 1 FROM `survey_".$num."` LIMIT 1;")
					->fetch();
			$tableExists = true;
		} catch (\Exception $e) {
			$tableExists = false;
		}
		$this->assertTrue($tableExists);
		try {
			$result = DB::connection('limemysql')
					->getPdo()
					->query("SELECT 1 FROM `survey_".$num."_timings` LIMIT 1;")
					->fetch();
			$tableTimingsExists = true;
		} catch (\Exception $e) {
			$tableTimingsExists = false;
		}
		$this->assertTrue($tableTimingsExists);

	}

	/**
	 * @group limebad
	 */
	public function testDeleteTable()
	{
		Artisan::call('limesurvey:tablefix');

		$num = '9999999';
		try {
			$result = DB::connection('limemysql')
					->getPdo()
					->query("SELECT 1 FROM `survey_".$num."` LIMIT 1;")
					->fetch();
			$tableExists = true;
		} catch (\Exception $e) {
			$tableExists = false;
		}
		$this->assertFalse($tableExists);
		try {
			$result = DB::connection('limemysql')
					->getPdo()
					->query("SELECT 1 FROM `survey_".$num."_timings` LIMIT 1;")
					->fetch();
			$tableTimingsExists = true;
		} catch (\Exception $e) {
			$tableTimingsExists = false;
		}
		$this->assertFalse($tableTimingsExists);

	}

}
