<?php namespace Pivotal\Cms\Controllers;

class BaseCmsController extends \BaseController
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
                return true;

            case User::DEPARTMENT_HEAD:
                return true;

            case User::TEACHER:
                return true;
        }

        return false;
    }
}