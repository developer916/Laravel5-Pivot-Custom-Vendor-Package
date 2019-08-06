<?php namespace Pivotal\School\Controllers;
use \Auth;
use \User;
use Pivotal\School\Models\SchoolInterface;

class BaseSchoolController extends \BaseController
{
    /**
     * Does the current user have permission to access this.
     */
    public static function can_access(SchoolInterface $school) {

        // check the role based permissions
        switch (Auth::user()->role) {

            case User::PIVOT_ADMIN:
                // can do anything
                return true;

            case User::SCHOOL_ADMIN:
            case User::CAMPUS_LEADER:
                // can access anything in their school
                return (Auth::user()->school_id == $school->id);

            case User::DEPARTMENT_HEAD:
            case User::TEACHER:
                // has no rights to access the school
                break;
        }

        return false;
    }
}