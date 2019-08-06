<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Userbak extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	public $timestamps = true;

	const PIVOT_ADMIN = 'pivot_admin';
	const SCHOOL_ADMIN = 'school_admin';
    const DEPARTMENT_HEAD = 'department_head';
    const TEACHER = 'teacher';

    protected $fillable = ['school_id', 'department_id', 'email', 'name', 'role'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

    public function get_departments() {
        return Department::join('classes', 'departments.id', '=', 'classes.department_id')
                         ->where('classes.teacher_id', $this->id)->get();
    }

    public function get_classes() {
        return $classes = aClass::where('teacher_id', '=', $this->id)->get();
    }

    public static function get_survey_ids($user_id) {
        $classes = aClass::where('teacher_id', '=', $user_id)->get();
        $survey_ids = array();
        foreach ($classes as $class) {
            $survey_ids[]= $class->limesurvey_id;
        }
        return $survey_ids;
    }

    public static function admins_for_school($school_id) {
        return self::where('school_id', '=', $school_id)->where('role', '=', User::SCHOOL_ADMIN)->get();
    }

    public static function get_by_email($email) {
        return User::where('email', 'like', $email)->first();
    }

    public static function create_user_with_password_email(User $user) {
        if (empty($user->password)) {
            $password = Utils::random_password(10);
        } else {
            $password = $user->password;
        }
        $user->password = Hash::make($password);
        $user->save();

        Mail::send('emails.auth.password', array('user' => $user, 'password' => $password), function($message) use($user) {
            $message->to($user->email, $user->name)->subject('Welcome!');
        });

        return $user;
    }

    /**
     * Fetch mutator for Editor property
     *
     * @return void
     */
    public function getEditorAttribute() {

        switch($this->role) {
            case User::PIVOT_ADMIN:
            case User::SCHOOL_ADMIN:
                return true;
                break;
        }

        return false;
    }

    /**
     * Fetch mutator for Administrator property
     *
     * @return void
     */
    public function getAdministratorAttribute() {

        return ($this->role == User::PIVOT_ADMIN);
    }

    /**
     * Teachers belong to a School
     */
    public function school() {
        return $this->belongsTo('School');
    }

    /**
     * Teachers have many classes
     */
    public function classes() {
        return $this->hasMany('aClass', 'teacher_id')->orderBy('name');
    }

    /**
     * Teachers mays be the Head of a Department
     */
    public function department() {
        return $this->belongsTo('Department');
    }

    /**
     * Teachers have many departments, via their classes
     */
    public function departments() {

        $departments = array();

        foreach (aClass::where('teacher_id', $this->id)->get() as $class) {
            foreach (Department::where('id', $class->department_id)->get() as $department) {
                $departments[$department->id] = $department;
            }
        }

        uasort($departments, function($a,$b) {
            return strcmp($a->name, $b->name);
        });

        return $departments;
    }

    /**
     * Login as a user
     * @param int $user_id user id you want to login as
     */
    public function login_as ($user_id) {
        $user = User::find($user_id);

        if ($user) {
            $orig_id = $this->id;
            Auth::login($user);
            Session::put('orig_id', $orig_id);
        }
    }

    /**
     * Logout of the masquaraded user
     */
    public function logout_as () {
        $orig_id = Session::pull('orig_id');
        if ($orig_id) {
            $user = User::find($orig_id);
            Auth::login($user);
        }
    }
}
