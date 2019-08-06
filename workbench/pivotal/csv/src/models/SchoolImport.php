<?php namespace Pivotal\Csv\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Pivotal\Campus\Models\Campus;
use Pivotal\Course\Models\Course;
use Pivotal\Csv\Row;
use Pivotal\Csv\RowInterface;
use Pivotal\Department\Models\Department;
use Pivotal\School\Models\SchoolInterface;
use Pivotal\User\Models\User;

class SchoolImport extends Model implements RowInterface
{

    private $errors = array();
    private $school;
    private $user;
    private $valid = false;
    private $headerRow;


    /**
     * Returns a clone of the current model instance with its attributes cleared
     * @return SchoolImport
     */
    public function getInstance()
    {
        $new_instance = clone $this;
        $new_instance->fill([]);

        return $new_instance;
    }

    /**
     * Assign the header row to this row
     * @param RowInterface $row
     */
    public function setHeaderRow(RowInterface $row)
    {
        $this->headerRow = $row;
    }


    /**
     * Validate that the required fields have correct values and add entities where appropriate
     * @param array $options
     * @return $this
     */
    public function save(array $options = array())
    {
        Event::fire('csv.schoolimport.save.before', array($this));
        if ($this->isValid()) {

            //Create Department if it does not already exist
            $existing_department =
                Department::where('name', '=', $this->department)
                    ->where('school_id', '=', $this->school->id)->first();
            //If the department exists
            if ($existing_department) {
                $this->department = $existing_department;
            } else {
                $this->department = new Department(array('name' => $this->department));
                $this->department->school()->associate($this->school)->save();
                $this->school->departments()->save($this->department);
            }

            //Create User if they do not already exist

            $existing_user =
                User::where('email', '=', $this->email)->first();

            //If the user exists
            if ($existing_user) {
                $this->user = $existing_user;
            } else {
                $user_data = array(
                    'school_id' => $this->school->id,
                    'department_id' => ($this->role == User::DEPARTMENT_HEAD) ? $this->department->id : null,
                    'email' => $this->email,
                    'name' => $this->teacher,
                    'role' => $this->role
                );

                $this->user = new User($user_data);
                //@todo refactor this so the User model handling package knows what to do with a new user w/o pw
                $this->user = \Pivotal\User\Models\User::create_user_with_password_email($this->user);
                $this->user->save();
            }


            // check if school can have campuses
            if ($this->school->is_campused && isset($this->campus) && $this->campus != '') {
                //Create campus if they do not already exist
                $existing_campus =
                    Campus::where('code', '=', $this->campus)->where('school_id', '=', $this->school->id)->first();

                //If the campus exists
                if ($existing_campus) {
                    $campus = $existing_campus;
                } else {

                    $campus_data = array(
                        'school_id' => $this->school->id,
                        'code' => $this->campus
                    );

                    $campus = new Campus($campus_data);
                    $campus->save();
                }
                $campusId = $campus->id;
            } else {
                $campusId = null;
            }

            $start_year = date('Y');

            //Create Class if the class does not already exist
            $existing_class = Course::with(array('teacher' => function ($query) {
                return $query->where('school_id', $this->school->id);
            }))->where('code', '=', $this->code)->where('start_year', '=', $start_year)->first();


            if ($existing_class) {
                if (!$existing_class->teacher || !$existing_class->teacher->school_id == $this->school->id) unset($existing_class);
            }


            $existing_class =
                Course::where('code', '=', $this->code)->where('start_year', '=', $start_year)
                    ->where('department_id', '=', $this->department->id)->first();

            //dd($existing_class->toArray());

            //If the class exists
            if ($existing_class) {
                $className = $this->class;
                $this->class = $existing_class;
                if ($campusId) {
                    $this->class->campus_id = $campusId;
                    $this->class->save();
                }
                $this->class->teacher_id = $this->user->id;
                $this->class->name = $className;
                $this->class->year_level = $this->getAttribute('year level');
                $this->class->num_students = $this->getAttribute('number of students');
                $this->class->save();
            } else {
                $class_data = array(
                    'teacher_id' => $this->user->id,
                    'department_id' => $this->department->id,
                    'name' => $this->class,
                    'code' => $this->code,
                    'year_level' => $this->getAttribute('year level'),
                    'num_students' => $this->getAttribute('number of students'),
                    'start_year' => $start_year
                );
                if ($campusId) {
                    $class_data['campus_id'] = $campusId;
                }
                $this->class = new Course($class_data);
                $this->class->save();
            }

        } elseif (!$this->isValid()) {

            //@todo do something if this is not valid?
        }

        return $this;
    }

    /**
     * @param SchoolInterface $school
     * @return $this
     */
    public function setSchool(SchoolInterface $school)
    {
        $this->school = $school;
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return (bool)$this->valid;
    }


    /**
     * @return $this
     */
    public function setInvalid()
    {
        $this->valid = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function setValid()
    {
        $this->valid = true;
        return $this;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors = array())
    {
        $this->errors = $errors;
    }


    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}