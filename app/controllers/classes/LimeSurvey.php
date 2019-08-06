<?php

/**
 * Remote control class for controlling lime survey. This class will make calls to RPC-JSON remote control
 * of a lime survey site
 *
 * @package
 * @subpackage
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

require_once(__DIR__.'/../lib/jsonRPCClient.php');
require_once(__DIR__.'/LimeSurveyException.php');

class LimeSurvey {

    private $client = null;
    private $sessionkey = null;
    private $validproperties = array(
        'active','autonumber_start','emailnotificationto','nokeyboard','showwelcome','additional_languages','autoredirect','emailresponseto','owner_id','showxquestions',
        'admin','bounce_email','expires','printanswers','adminemail','bounceaccountencryption','faxto','publicgraphs','startdate','alloweditaftercompletion','bounceaccounthost',
        'format','publicstatistics','templateallowjumps','bounceaccountpass','googleanalyticsapikey','refurl','tokenanswerspersistence','allowprev','bounceaccounttype',
        'googleanalyticsstyle','savetimings','tokenlength','allowregister','bounceaccountuser','htmlemail','sendconfirmation','usecaptcha','allowsave','bounceprocessing',
        'ipaddr','showgroupinfo','usecookie','anonymized','bouncetime','language','shownoanswer','usetokens','assessments','datecreated','listpublic','showprogress',
        'attributedescriptions','datestamp','navigationdelay','showqnumcode','sid');

    /**
     * Instantiate a LimeSurvey remote procedure call with the URL and the Admin and password
     */
    public function __construct($serviceurl = null, $username = null, $password = null, $debug = false) {

        // default to the values in the config file
        $serviceurl = $serviceurl ?: Config::get('limesurvey.serviceurl');
        $username =  $username ?: Config::get('limesurvey.username');
        $password =  $password ?: Config::get('limesurvey.password');

        $this->client = new jsonRPCClient($serviceurl, $debug);
        $this->sessionkey = $this->login($username, $password);
    }

    /**
     * List all the surveys in the system.
     * @return array an associative array of surveyid => survey. Where a survey is an object:
     *      stdClass {
     *          sid: survey id
     *          surveyls_title: the survey title
     *          startdate: survey start date
     *          expires: survey expiration date
     *          active: survey is active or not (Y or N)
     *      }
     */
    public function list_surveys() {
        $surveys = $this->client->list_surveys($this->sessionkey);
        $this->check_response($surveys, LimeSurveyException::LIST_SURVEY_ERROR);

        $surveyarray = array();
        foreach ($surveys as $survey) {
            $survey = (object)$survey;
            $surveyarray[$survey->sid] = $survey;
        }
        return $surveyarray;
    }

    /**
     * Add response to survey
     * @param int $sid the ID of the survey to answer
     * @param array $response response
     * @return int the ID of the new answer just created
     */
    public function add_response($sid, $response) {
        $new_sid = $this->client->add_response($this->sessionkey, $sid, $response);
        $this->check_response($new_sid, LimeSurveyException::SURVEY_ADD_RESPONSE);
        return $new_sid;
    }

    /**
     * Get questions for survey
     * @param int $sid the ID of the survey to answer
     * @return int the ID of the new answer just created
     */
    public function list_questions($sid) {
        $questions = $this->client->list_questions($this->sessionkey, $sid);
        $this->check_response($questions, LimeSurveyException::SURVEY_ADD_RESPONSE);
        return $questions;
    }

    /**
     * Copy a survey to a new one.
     * @param int $original_sid the ID of the survey to copy (that survey should exist when calling list_surveys
     * @param string $surveyname the name of the new survey
     * @return int the ID of the new survey just created
     */
    public function copy_survey($original_sid, $surveyname) {
        if (!trim($surveyname)) {
            throw new LimeSurveyException(LimeSurveyException::SURVEY_NAME_INVALID);
        }
        $new_sid = $this->client->copy_survey($this->sessionkey, $original_sid, $surveyname);
        return $new_sid;
    }

    /**
     * Delete a survey
     * @param int $sid the ID of the survey to delete
     */
    public function delete_survey($sid) {
        $result = $this->client->delete_survey($this->sessionkey, $sid);
        $this->check_response($result, LimeSurveyException::DELETE_SURVEY_ERROR);
    }

    /**
     * Set the properties of a survey
     * @param int $sid the survey ID
     * @param array $properties an associative name => value of the properties, listed at https://manual.limesurvey.org/RemoteControl_2_API#get_survey_properties
     * @return array the array of properties (basically the same as the one passed in)
     * @throws LimeSurveyException if properties are invalid or if there are some
     */
    public function set_survey_properties($sid, $properties) {
        $this->check_valid_properties(array_keys($properties));
        $result = $this->client->set_survey_properties($this->sessionkey, $sid, $properties);
        $this->check_response($result, LimeSurveyException::SURVEY_PROPERTY_INVALID);
        return $result;
    }

    /**
     * Get the properties of a survey
     * @param int $sid the survey ID
     * @param array $properties the array of properties string
     * @return array the array of properties (basically the same as the one passed in)
     * @throws LimeSurveyException if properties are invalid or if there are some
     */
    public function get_survey_properties($sid, $properties=null) {
        if ($properties!==null) {
            $this->check_valid_properties($properties);
        } else {
            $properties = $this->validproperties;
        }
        $response = $this->client->get_survey_properties($this->sessionkey, $sid, $properties);
        $this->check_response($response, LimeSurveyException::SURVEY_PROPERTY_INVALID);
        return $response;
    }

    /**
     * Activate a survey
     * @param int $sid the ID of the survey to activate
     */
    public function activate_survey($sid) {
        $result = $this->client->activate_survey($this->sessionkey, $sid);
        $this->check_response($result, LimeSurveyException::ACTIVATION_ERROR);
    }

    public function set_survey_date($startdate, $expirationdate) {

    }

    public function close() {
        $this->client->release_session_key();
    }

    public function __destruct() {
        $this->close();
    }

    private function login($username, $password) {
        $result = $this->client->get_session_key($username, $password);
        $this->check_response($result, LimeSurveyException::LOGIN_ERROR);
        return $result;
    }

    private function check_response($response, $errorcode) {
        if (is_array($response) && isset($response['status']) && $response['status'] != 'OK') {
            switch ($response['status']) {
                case 'OK':
                    // this is fine, no problem
                    break;
                case 'Invalid session key':
                    throw new LimeSurveyException(LimeSurveyException::SESSION_TIMED_OUT);
                case 'No permission':
                    throw new LimeSurveyException(LimeSurveyException::PERMISSION_ERROR);
                default:
                    throw new LimeSurveyException($errorcode, $response['status']);
            }
        }
    }

    private function check_valid_properties($properties) {
        $invalidproperties = array_diff($properties, $this->validproperties);
        if (!empty($invalidproperties)) {
            throw new LimeSurveyException(LimeSurveyException::SURVEY_PROPERTY_INVALID, implode(', ', $invalidproperties));
        }
    }
}
