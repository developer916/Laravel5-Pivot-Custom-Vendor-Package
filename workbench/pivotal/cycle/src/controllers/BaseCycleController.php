<?php namespace Pivotal\Cycle\Controllers;

use Pivotal\Cycle\Models\CycleInterface;
use \Auth;
use \User;
class BaseCycleController extends \BaseController
{
    /**
     * Does the current user have permission to access this.
     */
    public static function can_access(CycleInterface $cycle) {

        // check the role based permissions
        switch (Auth::user()->role) {

            case User::PIVOT_ADMIN:
                // can do anything
                return true;

            case User::SCHOOL_ADMIN:
            case User::CAMPUS_LEADER:
                // can access anything in their school
                return (Auth::user()->school_id == $cycle->school_id);

            case User::DEPARTMENT_HEAD:
                // can access anything in their department
                foreach ($cycle->classes as $class) {
                    if ($class->department->id == Auth::user()->department_id) {
                        return true;
                    }
                }
                return false;

            case User::TEACHER:
                foreach($cycle->classes as $class)
                {
                    if($class->teacher->id == Auth::user()->id)
                    {
                        return true;
                    }
                }
                // can access their own classes
                return false;
        }

        return false;
    }
}