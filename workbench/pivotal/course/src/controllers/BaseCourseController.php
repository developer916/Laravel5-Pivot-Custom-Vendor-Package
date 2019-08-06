<?php namespace Pivotal\Course\Controllers;

use Pivotal\Models\CourseInterface;
use \Auth;
use \User;
class BaseCourseController extends \BaseController
{
    /**
     * Does the current user have permission to access this.
     */
    public static function can_access(CourseInterface $class) {

        // check the role based permissions
        switch (Auth::user()->role) {

            case User::PIVOT_ADMIN:
                // can do anything
                return true;

            case User::SCHOOL_ADMIN:
            case User::CAMPUS_LEADER:
                // can access anything in their school
                return (Auth::user()->school_id == $class->department->school_id);

            case User::DEPARTMENT_HEAD:
                // can access anything in their department
                if (Auth::user()->id == $class->teacher_id) {
                    return true;
                }
                return (Auth::user()->department_id == $class->department_id);

            case User::TEACHER:
                // can access their own classes
                return (Auth::user()->id == $class->teacher_id);
        }

        return false;
    }
}