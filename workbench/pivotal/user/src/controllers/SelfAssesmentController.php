<?php namespace Pivotal\User\Controllers;

use \Auth;
use \Hash;
use \Request;
use \Validator;
use Pivotal\Department\Models\Department;
use Pivotal\School\Models\School;
use Pivotal\User\Models\User;
use \Redirect;
use Illuminate\Auth\UserInterface;
use Pivotal\User\Controllers\BaseUserController;
use \Pivotal\School\Models\SchoolInterface;
use Illuminate\Support\Facades\Input;
class SelfAssesmentController extends BaseSelfAssesmentController {

    public function index()
    {

    }

    public function view()
    {
        $teacher = Auth::user();
        $last_cycle = $teacher->cycles;

        $data = array(
            'header' => 'Self Assessment'
        );
        return $this->get_view('user::pages.selfassesment.view', $data);
    }




}