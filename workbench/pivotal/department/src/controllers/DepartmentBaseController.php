<?php namespace Pivotal\Department\Controllers;

use \Auth;
use \User;
use Pivotal\Department\Models\DepartmentInterface;

class DepartmentBaseController extends \BaseController
{

    /**
     * Does the current user have permission to access this.
     */
    public static function can_access(DepartmentInterface $department) {

//        echo Auth::user()->school_id;
//        echo "<br>";
//        echo $department->school->id;
//        echo "<br>";
//        echo $department->school_id;
//        echo "<br>";
//
//        echo Auth::user()->school_id == $department->school_id;
//        die();
        // check the role based permissions
        switch (Auth::user()->role) {

            case User::PIVOT_ADMIN:
                // can do anything
                return true;

            case User::SCHOOL_ADMIN:
            case User::CAMPUS_LEADER:
                // can access anything in their school
                return (Auth::user()->school_id == $department->school_id);

            case User::DEPARTMENT_HEAD:
                // can access anything in their department
                return (Auth::user()->department_id == $department->id);

            case User::TEACHER:
                // can access their own departments
                return $department->classes->filter(function($class) {
                    return (Auth::user()->id == $class->teacher_id);
                })->count();
        }

        return false;
    }
}