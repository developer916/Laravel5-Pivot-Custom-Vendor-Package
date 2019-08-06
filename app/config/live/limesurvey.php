<?php

/**
 * Lime survey configuration for the LIVE environemnt
 *
 * @package
 * @subpackage
 * @copyright  &copy; 2015 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */




return array(
        'db_prefix' => 'lime_',
        'surveyurl'  => 'http://survey.pivotpl.com/index.php/',
        'serviceurl' => 'http://survey.pivotpl.com/index.php/admin/remotecontrol',
        'adminurl'   => 'http://survey.pivotpl.com/index.php/admin/survey/sa/view/surveyid/',
        'username'   => 'admin',
        'password'   => '!9hQh4T4pKoowVt',
        /* if the template survey id is changed, please change line 6 of the
         * limesurvey/application/controllers/survey/index.php file in the limesurvey code folder
         */
        'template'   => 197974
);


