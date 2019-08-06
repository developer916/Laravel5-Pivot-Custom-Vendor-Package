<?php

/**
 * Class for querying the survey data
 *
 * @package
 * @subpackage
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

class LimeData {

    const QUESTION_INDEX_STATEMENT = 0;
    const QUESTION_INDEX_ID = 1;
    const QUESTION_INDEX_COUNT = 2;

    public function get_survey_question_average($survey_ids) {
        $statistics = $this->get_surveys_statistics($survey_ids);
        foreach ($statistics as $questionindex => $answerarray) {
            $statistics[$questionindex] = Utils::weighted_average_array($answerarray);
        }
        return $statistics;
    }

    /**
     * Get the statistics of a survey
     * @param array $survey_ids an array of lime survey ids
     * @return array array of question order => count array of each answer, e.g.
     *      array(
     *          1  => array(    // the first question
     *              1   => 3,   // first answer is chosen by 4 respondents
     *              2   => 5,
     *              3   => 4,
     *          ),
     *          2  => array(
     *              ....
     *          ),
     *      )
     */
    public function get_surveys_statistics($survey_ids) {
        $responsearray = array();

        foreach ($survey_ids as $sid) {
            if (!$sid) {
                continue;
            }
            $s_statistics = $this->get_survey_statistics($sid);
            foreach ($s_statistics as $questionindex => $answercount) {
                $originalarray = isset($responsearray[$questionindex]) ? $responsearray[$questionindex] : array();
                $responsearray[$questionindex] = Utils::sum_array($originalarray, $answercount);
            }
        }

        return $responsearray;
    }

    public function get_survey_statistics($sid) {
        $questions = $this->get_survey_questions($sid);

        $sql = "SELECT * FROM ". Config::get('limesurvey.db_prefix')."survey_$sid WHERE submitdate IS NOT NULL";
        $responses = DB::connection('limemysql')->select($sql);

        $responsearray = array();

        $i = 0;
        foreach ($questions as $q) {
            $responsearray[$i] = array_fill(1, 5, 0);
            foreach ($responses as $r) {
                $questionfield = $sid.'X'.$q->gid.'X'.$q->qid;
                $score = substr($r->$questionfield, 1);
                $responsearray[$i][$score]++;
            }
            $i++;
        }
        return $responsearray;
    }

    public function get_survey_responses($sid) {
        $questions = $this->get_survey_questions($sid);

        $sql = "SELECT * FROM ". Config::get('limesurvey.db_prefix')."survey_$sid WHERE submitdate IS NOT NULL";
        $responses = DB::connection('limemysql')->select($sql);

        $responsearray = array();

        $i = 0;

        foreach ($responses as $response) {
            $r = array();
            $r['id'] = $response->id;
            $r['submitdate'] = $response->submitdate;
            $r['startdate'] = $response->startdate;
            $r['datestamp'] = $response->datestamp;
            $r['lastpage'] = $response->lastpage;
            $r['startlanguage'] = $response->startlanguage;
            foreach ($questions as $q) {
                $questionfield = $sid.'X'.$q->gid.'X'.$q->qid;
                $r[$q->title] = $response->$questionfield;
            }
            $responsearray[] = $r;
        }

        return $responsearray;
    }

    /**
     * Get the count of submitted survey responses.
     *
     * @param int $sid Lime survey id
     * @return int count
     */
    public function count_survey_responses($sid) {

        $sql = "SELECT COUNT(*) AS count
                  FROM ". Config::get('limesurvey.db_prefix')."survey_$sid
                 WHERE submitdate IS NOT NULL";

        $response = DB::connection('limemysql')->selectOne($sql);

        return $response->count;
    }

    /**
     * Get field list for survey responses
     *
     * @param int $sid Lime survey id
     * @return array fields
     */
    public function get_survey_responses_fields($sid) {

        $sql = "SHOW COLUMNS
                  FROM ". Config::get('limesurvey.db_prefix')."survey_$sid";

        $responses = DB::connection('limemysql')->select($sql);
        $fields = [];

        foreach ($responses as $r) {
            if (strpos($r->Field, ''.$sid) === 0) {
                $fields[] = $r->Field;
            }
        }

        return $fields;
    }

    /**
     * Get a list of questions in the survey.
     */
    public function get_survey_questions($sid, $indexby=self::QUESTION_INDEX_STATEMENT) {
        $sql = "SELECT * FROM ". Config::get('limesurvey.db_prefix')."questions WHERE sid = ? ORDER BY gid, qid";
        $questions = DB::connection('limemysql')->select($sql, array($sid));
        $questionarray = array();

        if ($indexby==self::QUESTION_INDEX_STATEMENT) {
            foreach ($questions as $q) {
                $questionarray[$q->question] = $q;
            }
        } else if ($indexby==self::QUESTION_INDEX_ID) {
            foreach ($questions as $q) {
                $questionarray[$q->qid] = $q;
            }
        } else if ($indexby==self::QUESTION_INDEX_COUNT) {
            $i = 0;
            foreach ($questions as $q) {
                $questionarray[$i++] = $q;
            }
        }

        foreach ($questionarray as $question) {
            $question->question = strip_tags($question->question);
        }

        return $questionarray;
    }

    /**
     * Get list of standards
     */
    public function get_survey_standards($sid) {
        $sql = "SELECT * from lime_groups WHERE sid = ? ORDER BY group_order";
        $standards = DB::connection('limemysql')->select($sql, array($sid));

        return $standards;
    }
}
