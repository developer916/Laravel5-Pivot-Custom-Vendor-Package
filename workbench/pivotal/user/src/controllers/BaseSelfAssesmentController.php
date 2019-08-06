<?php namespace Pivotal\User\Controllers;

use Illuminate\Auth\UserInterface;

use \Auth;
use Pivotal\User\Models\User;

class BaseSelfAssesmentController extends \BaseController{
    /**
     * Does the current user have permission to access this.
     */
    public static function can_access(UserInterface $user) {

        // users can always access themselves
        if (Auth::user()->id == $user->id) {
            return true;
        }

        // check the role based permissions
        switch (Auth::user()->role) {

            case User::PIVOT_ADMIN:
                // can do anything
                return true;

            case User::SCHOOL_ADMIN:
            case User::CAMPUS_LEADER:
                // can access anything in their school
                return (Auth::user()->school_id == $user->school_id);

            case User::DEPARTMENT_HEAD:
                // can access anything in their department
                return Auth::user()->department->classes->filter(function($class) use ($user) {
                    return ($class->teacher_id == $user->id);
                })->count();

            case User::TEACHER:

                return Auth::user()->id == $user->id;
                // no rights to access other users
                break;
        }

        return false;
    }
}