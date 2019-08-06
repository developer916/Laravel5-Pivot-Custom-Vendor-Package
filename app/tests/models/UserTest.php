<?php

/**
 * Test class for user
 *
 * @package
 * @subpackage
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

class UserTest extends TestCase {

    public function test_get_survey_ids() {
        $survey_ids = User::get_survey_ids(1);
        var_dump($survey_ids);
    }

    public function test_create_user() {
        $user = new User();
        $user->school_id = 1;
        $user->email = 'test@mbg.edu.au';
        $user->role = User::TEACHER;
        $user->password = 'mypassword';
        $user->name = 'Test User';
        User::create_user_with_password_email($user);
    }

    public function test_get_departments() {
        $teacher = User::find(1);
        $departments = $teacher->departments;
        var_dump(count($departments));
        var_dump($departments[0]);
    }

}