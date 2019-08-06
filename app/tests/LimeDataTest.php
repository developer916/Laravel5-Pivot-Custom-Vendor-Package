<?php

/**
 * Testing file for LimeData
 *
 * @package    
 * @subpackage 
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

require_once('../controllers/classes/LimeData.php');

class LimeDataTest extends TestCase {

    private $limedata = null;
    private $main_sid = 197974;
    private $second_sid = 884132;

    private function get_lime_data() {
        return $this->limedata = new LimeData();
    }

    public function test_get_surveys_statistics() {
        $limedata = $this->get_lime_data();
        $statistics = $limedata->get_surveys_statistics(array($this->main_sid, $this->second_sid));
        $this->assertEquals(array('1' => 0, '2' => 3, '3' => 0, '4' => 0, '5' => 0), $statistics[0]);
        $this->assertEquals(array('1' => 0, '2' => 1, '3' => 0, '4' => 1, '5' => 1), $statistics[6]);
    }

    public function test_get_survey_statistics() {
        $limedata = $this->get_lime_data();
        $statistics = $limedata->get_survey_statistics($this->main_sid);
        $this->assertEquals(array('1' => 0, '2' => 2, '3' => 0, '4' => 0, '5' => 0), $statistics[0]);
        $this->assertEquals(array('1' => 0, '2' => 1, '3' => 0, '4' => 0, '5' => 1), $statistics[6]);
    }

    public function test_get_survey_questions() {
        $limedata = $this->get_lime_data();
        $questionsbystatement = $limedata->get_survey_questions($this->main_sid, LimeData::QUESTION_INDEX_STATEMENT);
        $this->assertCount(25, $questionsbystatement);
        $this->assertEquals(1, $questionsbystatement['This teacher helps me understand my strengths and weaknesses in this class.']->qid);
        $this->assertEquals(8, $questionsbystatement['This teacher encourages me to extend my vocabulary in this class.']->qid);

        $questionsbyids = $limedata->get_survey_questions($this->main_sid, LimeData::QUESTION_INDEX_ID);
        $this->assertCount(25, $questionsbyids);
        $this->assertEquals('This teacher helps me understand my strengths and weaknesses in this class.', $questionsbyids[1]->question);
        $this->assertEquals('This teacher encourages me to extend my vocabulary in this class.', $questionsbyids[8]->question);
    }
}