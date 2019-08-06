<?php

/**
 * Test class for school
 *
 * @package
 * @subpackage
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

class SchoolTest extends TestCase {

    public function test_get_department() {
        $school = School::find(1);
        $departments = $school->departments;
        $this->assertEquals(3, count($departments));
    }

    public function test_get_users() {
        $school = School::find(1);
        $users = $school->users;
        $this->assertEquals(3, count($users));
        var_dump($users);
    }

    public function test_get_classes() {
        $school = School::find(1);
        $classes = $school->classes;
        var_dump($classes[0]);
    }
}