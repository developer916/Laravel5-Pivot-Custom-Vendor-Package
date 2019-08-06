<?php

/**
 * Exception class for LimeSurvey
 *
 * @package    
 * @subpackage 
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

class LimeSurveyException extends Exception {
    const LOGIN_ERROR = 1;
    const SURVEY_NAME_INVALID = 2;
    const LIST_SURVEY_ERROR = 3;
    const SESSION_TIMED_OUT = 4;
    const PERMISSION_ERROR = 5;
    const DELETE_SURVEY_ERROR = 6;
    const ACTIVATION_ERROR = 7;
    const SURVEY_PROPERTY_INVALID = 8;
    const SURVEY_ADD_RESPONSE = 9;

    private static $error_message = array(
        self::LOGIN_ERROR => 'Error logging in',
        self::SURVEY_NAME_INVALID => 'Survey name cannot be empty',
        self::LIST_SURVEY_ERROR => 'Listing survey error',
        self::SESSION_TIMED_OUT => 'Session timed out',
        self::PERMISSION_ERROR => 'Not enough permission',
        self::DELETE_SURVEY_ERROR => 'Error deleting survey',
        self::ACTIVATION_ERROR => 'Error activating survey',
        self::SURVEY_PROPERTY_INVALID => 'Invalid survey property',
        self::SURVEY_ADD_RESPONSE => 'Error adding response',
    );

    public function __construct($code, $additionalmessage='', $previous=null) {
        $message = self::$error_message[$code];
        if ($additionalmessage) {
            $message .= ': '.$additionalmessage;
        }
        parent::__construct($message, $code, $previous);
    }

}
