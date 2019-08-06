<?php

/**
 * Test class for department
 *
 * @package
 * @subpackage
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

class DepartmentTest extends TestCase {

    public function test_get_survey_ids() {
        $survey_ids = Department::get_survey_ids(1);
        var_dump($survey_ids);
    }

    public function test_get_teachers() {
        $department = Department::find(1);
        $teachers = $department->teachers;
        foreach ($teachers as $teacher) {
            var_dump($teacher->id);
        }
    }

    public function test_get_by_name() {
        $deps = Department::get_by_name('Mathematics', 1);
        $this->assertEquals(1, count($deps));
        $this->assertEquals('Mathematics', $deps[0]->name);
    }
}