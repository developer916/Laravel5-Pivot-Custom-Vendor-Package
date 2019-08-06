<?php
use Illuminate\Auth\UserInterface;
use \Pivotal\School\Models\SchoolInterface;
use Illuminate\Support\Facades\Input;
class UsersController extends BaseController {

    /**
     * Serve the login page
     */
    public function login() {

        return $this->get_view('user-login', ['header'=>'Login']);
    }

    /**
     * Authenticate a User
     */
    public function authenticate() {

        if (Auth::attempt(array('email' => Input::get('email'), 'password' => Input::get('password')))) {
            return Redirect::intended('/');
        }

        // bounce back to the login page
        return Redirect::to('/login')->withErrors(['email'=>' ', 'password'=>'Incorrect username or password.']);
    }


    /**
     * Login as a user
     * @param int $user_id
     */
    public function login_as ($user_id) {

        if (Auth::user()->role != User::PIVOT_ADMIN) {
            return Redirect::to("/")->with('error', 'Only Pivot Admins can login as someone else');
        }

        Auth::user()->login_as($user_id);
        return Redirect::to('/');
    }

    /**
     * Logout and redirect to the login page
     */
    public function logout() {

        Auth::logout();

        return Redirect::to('/login');
    }

    /**
     * Logout and redirect to the login page
     */
    public function logout_as() {

        Auth::user()->logout_as();

        return Redirect::to('/');
    }

    /**
     * List all the Users
     */
    public function index() {

        $data = array();
        $data['header'] = 'Users';
        $data['users'] = User::whereIn('role', array(User::SCHOOL_ADMIN, User::PIVOT_ADMIN))->get();

        return $this->get_view('user-index', $data);
    }

    /**
     * View a User
     */
    public function view(UserInterface $user) {

        $data = array();
        $data['header'] = $user->name;
        $data['user'] = $user;
        $data['school'] = $user->school;
        $data['departments'] = $user->departments();
        $data['classes'] = $user->classes;

        // to avoid to much if else in user-view template, we create an empty school
        if ($user->school) {
            $school = $user->school;
        } else {
            $school = new School();
        }
        $data['school'] = $school;

        return $this->get_view('user-view', $data)
                    ->nest('departments_panel', 'department::includes.departments-panel', $data)
                    ->nest('classes_panel', 'course::includes.classes-panel', $data);
    }
    /**
     * Create a User
     */
    public function create(SchoolInterface $school = null) {

        $user = new User();

        if ($school) {
            $user->school_id = $school->id;
        }

        return $this->edit($user);
    }

    /**
     * Edit a User
     */
    public function edit(UserInterface $user = null) {

        $user = $user ?: new User;

        $roles = array(
            User::SCHOOL_ADMIN    => 'School admin',
            User::DEPARTMENT_HEAD => 'Department head',
            User::TEACHER         => 'Teacher'
        );

        // only an admin can make admins
        if (Auth::user()->isAdministrator()) {
            $roles = [User::PIVOT_ADMIN => 'Pivot admin'] + $roles;
        }

        $data = array();
        $data['header'] = 'Administration | ' . ($user->id ? 'Edit '.$user->name : 'Add new user');
        $data['user'] = $user;
        $data['school'] = School::find($user->school_id);
        $data['schools'] = [''=>''] + School::lists('name', 'id');
        $data['departments'] = [''=>''] + Department::where('school_id', $user->school_id)->lists('name', 'id');
        $data['roles'] = [''=>''] + $roles;
        if ($user->id) {
            $data['editusermodal'] = '
                <ul>
                    <li>As school admin, you may edit a user\'s details - role (Teacher, Department Head or School Admin), Department (only needed if the user is a Department Head), full name, email address and password.</li>
                    <li>If changes are made to a user\'s password, the new password will need to be separately advised to the user as no automatic email from the system will be issued.</li>
                </ul>
            ';
        } else {
            $data['editusermodal'] = '
                <ul>
                    <li>Select the new user\'s Role from the drop down list.
                        <ul>
                            <li>If the new user is a Department Head, select the relevant Department from the drop down list.</li>
                            <li>If the new user is a Teacher or School Admin, leave the "Department" line empty</li>
                        </ul>
                    </li>
                    <li>Type in the new user\'s Name and Email Address.</li>
                    <li>Type in a generic password for the new user.</li>
                    <li>Note, you will need to advise the new user of their login details (including their generic password) as no automatic email from the system will be issued.</li>
                </ul>
            ';
        }


        return $this->get_view('user-edit', $data);
    }

    /**
     * Save a User
     */
    public function save($data) {
        $data = json_decode($data);
        if ($data->school_id) {
            $school = School::find($data->school_id);
            if (!SchoolController::can_access($school)) {
                return Redirect::to("/")->with('error', 'Cannot edit this user!');
            }
        } else {
            $school = null;
        }
        if ($data->user_id) {
            $user = User::find($data->user_id);
            if (!UsersController::can_access($user)) {
                return Redirect::to("/")->with('error', 'Cannot edit this user!');
            }
        } else {
            $user = null;
        }
        // get all the data
        $data = Input::all();

        // compose the list of static rules
        $rules = array(
            'role'       => 'required',
            'name'       => 'required',
            'email'      => 'required|email|unique:users,email'.($user ? ','.$user->id : ''),
            'password'   => 'same:password2|required_with:password2|min:6'.(!$user ? '|required' : ''),
        );

        // override the default lang strings
        $messages = array(
            'email.unique'       => 'That email address is already in use.',
            'password.same'      => 'The password fields must match.',
            'required_with'      => 'The password fields must match.',
            'school_id.required' => 'The school field is required.',
            'school_id.in'       => 'School should not be set for users with a role of Pivot admin.',
            'department_id.required' => 'The department field is required.',
            'department_id.in'   => 'Department should only be set for users with a role of Department head.'
        );

        // instantiate the validator
        $validator = Validator::make($data, $rules, $messages);

        // add the conditonal rules
        $validator->sometimes('school_id', 'required|exists:schools,id', function($input) {
            // must choose a school if the user is not a pivot admin
            return $input->role != User::PIVOT_ADMIN;
        });

        $validator->sometimes('school_id', 'in:', function($input) {
            // school id must be null if user is a pivot admin
            return $input->role == User::PIVOT_ADMIN;
        });

        $validator->sometimes('department_id', 'required|exists:departments,id', function($input) {
            // must choose a department if the user is a department head
            return $input->role == User::DEPARTMENT_HEAD;
        });

        $validator->sometimes('department_id', 'in:', function($input) {
            // department id must be null if user is not a department head
            return $input->role != User::DEPARTMENT_HEAD;
        });

        // check the validation rules
        if ($validator->fails()) {
            Request::flash(); // preserve all the input data for repopulating the form
            return Redirect::back()->withErrors($validator);
        }

        $message = 'User successfully ' .( ($user) ? 'updated' : 'created');

        $user = $user ?: new User;
        $user->fill($data);
        $user->school_id = $user->school_id ?: NULL;
        $user->department_id = $user->department_id ?: null;
        if (Input::get('password')) {
            $user->password = Hash::make(Input::get('password'));
        }
        $user->save();

        // redirect to view page
        return Redirect::to("/user/view/{$user->id}")->with('message', $message);
    }

    /**
     * Delete a User
     */
    public function delete(UserInterface $user) {

        $user->delete();

        return Redirect::back()->with('message', 'User successfully deleted');
    }

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

        	case User::SCHOOL_ADMIN;
        	    // can access anything in their school
        	    return (Auth::user()->school_id == $user->school_id);

        	case User::DEPARTMENT_HEAD:
                // can access anything in their department
        	    return Auth::user()->department->classes->filter(function($class) use ($user) {
        	        return ($class->teacher_id == $user->id);
        	    })->count();

        	case User::TEACHER:
                // no rights to access other users
        	    break;
        }

        return false;
    }
}