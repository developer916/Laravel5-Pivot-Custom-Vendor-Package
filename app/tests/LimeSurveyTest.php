<?php

class LimeSurveyTest extends TestCase {

    private $limesurvey = null;
    const main_sid = 197974;

    private function limeSurveyInit() {
        if (!$this->limesurvey) {
            $url = Config::get('limesurvey.serviceurl');
            $username = Config::get('limesurvey.username');
            $password = Config::get('limesurvey.password');
            $this->limesurvey = new LimeSurvey($url, $username, $password, true);
        }
        return $this->limesurvey;
    }

	/**
	 * Test lime survey connection
	 */
	public function test_connection_sucess() {
        $this->limeSurveyInit();
	}

    /**
     * Test exception thrown correctly when login failed
     */
	public function test_login_failed() {
        try {
            $url = Config::get('limesurvey.serviceurl');
            $username = Config::get('limesurvey.username');
            $password = Config::get('limesurvey.password').'!';
            $limesurvey = new LimeSurvey($url, $username, $password, true);
            $this->assertTrue(false, 'Login should failed! LimeSurveyException expected to be thrown');
        } catch (Exception $ex) {
            $this->assertTrue($ex instanceof LimeSurveyException, 'Failed login exception is not an instance of LimeSurveyException');
        }
	}

    public function test_list_survey() {
        $limesurvey = $this->limeSurveyInit();
        $surveys = $limesurvey->list_surveys();
        $this->assertTrue(count($surveys) > 0);
    }

    public function test_clone_survey() {
        $limesurvey = $this->limeSurveyInit();
        $newid = $limesurvey->copy_survey(self::main_sid, 'Test cloned survey');

        $surveys = $limesurvey->list_surveys();
        $this->assertTrue(isset($surveys[$newid]), 'Copied survey should exist');
        $newsurveyproperties = $limesurvey->get_survey_properties($newid, array('sid', 'active'));
        $this->assertTrue($newsurveyproperties['active']=='N', 'New survey should not be activated');

        $limesurvey->activate_survey($newid);
        $newactivatedproperties = $limesurvey->get_survey_properties($newid, array('sid', 'active'));
        $this->assertTrue($newactivatedproperties['active']=='Y', 'New survey should be activated');

        $limesurvey->delete_survey($newid);
        $surveyafterdelete = $limesurvey->list_surveys();
        $this->assertFalse(isset($surveyafterdelete[$newid]), 'Deleted survey should be removed');
    }

    public function test_lime_survey_close() {
        $limesurvey = $this->limeSurveyInit();
        $limesurvey->close();
    }

}
