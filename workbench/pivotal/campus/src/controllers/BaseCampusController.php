<?php namespace Pivotal\Campus\Controllers;

use Pivotal\Campus\Models\CampusInterface;
use \Auth;
use \User;
class BaseCampusController extends \BaseController
{
    /**
     * Does the current user have permission to access this.
     */
    public static function can_access(CampusInterface $class) {

        // check the role based permissions
        switch (Auth::user()->role) {

            case User::PIVOT_ADMIN:
                // can do anything
                return true;

            case User::SCHOOL_ADMIN:
            case User::CAMPUS_LEADER:
                // can access anything in their school
                return (Auth::user()->school_id == $class->department->school_id);
        }

        return false;
    }
}