<?php namespace Pivotal\User\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Pivotal\Course\Models\Course;
use Pivotal\Department\Models\Department;
use Pivotal\Survey\Models\Assessment;
use Pivotal\Survey\Models\SelfAssessment;
use Pivotal\Survey\Models\Survey;
use Pivotal\Cycle\Models\Cycle;
use \Auth;
use Pivotal\User\Models\Relations\UserSelfAssessmentRelation;
use \Session;
use \Utils;
use \Hash;
use \Mail;

use Pivotal\User\Models\Relations\UserDepartmentsRelation;
use Pivotal\User\Models\Relations\UserCyclesRelation;
use Pivotal\User\Models\Relations\UserSurveysRelation;


class User extends Eloquent implements UserInterface, RemindableInterface
{
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
    const CAMPUS_LEADER = 'campus_leader';
    const DEPARTMENT_HEAD = 'department_head';
    const TEACHER = 'teacher';

    protected $fillable = ['school_id', 'department_id', 'email', 'name', 'role','password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token');


    public function setMetaAttribute($value = [])
    {
        //If an array was passed
        if(is_array($value))
        {
            foreach($value as $k => $v)
            {
                //If there is an existing meta value related to the user
                if($this->meta->has($k))
                {

                    //Change existing meta if it exsits
                    $alter_value = $this->meta->get($k);
                    $alter_value->value = $v;
                    $alter_value->user_id = $this->id;
                    $alter_value->save();

                }else{

                    //The value does not yet exist
                    $newMeta = new UserMeta();
                    $newMeta->key = $k;
                    $newMeta->value = $v;
                    $newMeta->user_id = $this->id;
                    $newMeta->save();
                }
            }


        }
        return $this;
    }



    public function meta()
    {
        return $this->hasMany('Pivotal\User\Models\UserMeta');
    }


    /**
     * Teachers belong to a School
     */
    public function school()
    {
        return $this->belongsTo('School');
    }

    /**
     * Teachers mays be the Head of a Department
     */
    public function department()
    {
        return $this->belongsTo('Department');
    }

    public function departments()
    {
        $related = new Department();
        $parent = $this;

        return new UserDepartmentsRelation($related->newQuery(), $this);
    }

    /**
     * Teachers have many classes
     */
    public function classess()
    {
        return $this->hasMany('Pivotal\Course\Models\Course', 'teacher_id')->orderBy('name');
    }

    public function classes()
    {
        //@todo refactor this out -- Alias for courses()
        return $this->courses();
    }

    /**
     * Schools have many classes, which belong to the school's departments
     */
    public function classesForYear($year = false) {
        if (!$year) {
            $year = date('Y');
        }
        return $this->courses()->where('start_year', $year)->get();
    }

    public function courses()
    {
        return $this->hasMany('Pivotal\Course\Models\Course', 'teacher_id', 'id');
    }

    public function cycles()
    {
        $related = new Cycle();
        $parent = $this;

        return new UserCyclesRelation($related->newQuery(), $this);
    }


    public function isSelfAssessmentComplete()
    {
        $now = new \DateTime('now');

        if (count($this->classesForYear()) == 0) {
            return false;
        }

        $latest_cycle = $this->cycles()->orderBy('start_date',"DESC")->first();
        if (!$latest_cycle) {
            return false;
        }
        $cycle_end = \DateTime::createFromFormat('d/m/Y',$latest_cycle->end_date);

        if($cycle_end < $now) return true;

        $survey = Assessment::where('cycle_id','=',$latest_cycle->id)->where('teacher_id','=',$this->id)->where('q1','!=','null');

        if($survey->first()) return true;
        return false;

    }

    public function getAssessmentStatus()
    {
        $now = new \DateTime('now');

        if (count($this->classesForYear()) == 0) {
            return Assessment::STATUS_UNAVAILABLE;
        }

        $latest_cycle = $this->cycles()->orderBy('start_date',"DESC")->first();
        if (!$latest_cycle) {
            return Assessment::STATUS_UNAVAILABLE;
        }
        $cycle_end = \DateTime::createFromFormat('d/m/Y',$latest_cycle->end_date);

        if($cycle_end < $now) return Assessment::STATUS_UNAVAILABLE;

        $survey = Assessment::where('cycle_id','=',$latest_cycle->id)->where('teacher_id','=',$this->id)->where('q1','!=','null');

        if($survey->first()) return Assessment::STATUS_COMPLETED;
        return Assessment::STATUS_INCOMPLETE;

    }


    public function selfAssessment()
    {
        $related = new SelfAssessment();
        if (isset($this->self_sid) && $self_assessment = $related->where('sid', '=', $this->self_sid)->first()) {
            if (isset($self_assessment->sid)) {
                return $self_assessment;
            }
        }
        return new SelfAssessment();
    }

    public function proxies()
    {
        return $this->belongsToMany('Pivotal\User\Models\User','user_proxies','proxy_id','user_id');
    }


    public function surveys()
    {
        $related = new Survey();
        return new UserSurveysRelation($related->newQuery(), $this);
    }

    public function isTeacher()
    {
        return $this->role == self::TEACHER;
    }

    public function isDepartmentHead()
    {
        return $this->role == self::DEPARTMENT_HEAD;
    }

    public function isSchoolAdministrator()
    {
        return $this->role == self::SCHOOL_ADMIN;
    }

    public function isAdministrator()
    {
        return $this->role == self::PIVOT_ADMIN;
    }


    public function isSuperAdmin()
    {
        if($this->role == self::PIVOT_ADMIN)
        {
            return true;
        }
        return false;
    }

    public function isEditor()
    {
        if($this->role == self::PIVOT_ADMIN || $this->role == self::SCHOOL_ADMIN)
        {
            return true;
        }
        return false;
    }


    //@todo refactor $teacher->surveys->lists('sid');
    public static function get_survey_ids($user_id)
    {
        $classes = Course::where('teacher_id', '=', $user_id)->get();
        $survey_ids = array();
        foreach ($classes as $class) {
            $survey_ids[] = $class->limesurvey_id;
        }
        return $survey_ids;
    }

//    public static function admins_for_school($school_id) {
//        return self::where('school_id', '=', $school_id)->where('role', '=', User::SCHOOL_ADMIN)->get();
//    }


//@todo refactor -- remove
    public static function create_user_with_password_email(User $user)
    {
        if (empty($user->password)) {
            $password = Utils::random_password(10);
        } else {
            $password = $user->password;
        }

        $user->password = \Hash::make($password);

        $user->save();


        \Mail::send('emails.auth.password', array('user' => $user, 'password' => $password), function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Pivot student survey tool: your login details');
        });

        return $user;
    }

    public static function create_user_with_new_password_email(User $user)
    {
        $password = Utils::random_password(10);
        $user->password = \Hash::make($password);
        $user->save();

        \Mail::send('emails.auth.password', array('user' => $user, 'password' => $password), function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Pivot student survey tool: your login details');
        });

        return $user;
    }

    //@todo refactor -- remove
    /**
     * Login as a user
     * @param int $user_id user id you want to login as
     */
    public function login_as($user_id)
    {
        $user = User::find($user_id);

        if ($user) {
            $orig_id = $this->id;
            \Auth::login($user);
            \Session::put('orig_id', $orig_id);
        }
    }

    /**
     * Logout of the masquaraded user
     */
    public function logout_as()
    {
        $orig_id = \Session::pull('orig_id');
        if ($orig_id) {
            $user = User::find($orig_id);
            \Auth::login($user);
        }
    }
}