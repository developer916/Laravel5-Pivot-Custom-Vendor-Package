<?php

/**
 * Create Lime survey test
 *
 * @package
 * @subpackage
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

class CreateLimeSurveyTest extends TestCase {

    const main_sid = 197974;
    private $limesurvey = null;

    private function limeSurveyInit() {
        if (!$this->limesurvey) {
            $url = Config::get('limesurvey.serviceurl');
            $username = Config::get('limesurvey.username');
            $password = Config::get('limesurvey.password');
            $this->limesurvey = new LimeSurvey($url, $username, $password, true);
        }
        return $this->limesurvey;
    }

    public function test_create_survey_for_classes() {
        $allclasses = DB::select('select * from classes');
        $limesurvey = $this->limeSurveyInit();
        foreach ($allclasses as $class) {
            if (!$class->limesurvey_id) {
                $sid = $limesurvey->copy_survey(self::main_sid, $class->name.' Yr'.$class->year_level.' ('.$class->code.')'.' Survey');
                DB::update('UPDATE classes SET limesurvey_id=? WHERE id = ?', array($sid, $class->id));
            }
        }
    }

}