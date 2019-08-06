<?php

use Codesleeve\Stapler\ORM\EloquentTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Departmentbak extends Eloquent {
    use EloquentTrait;

    protected $table = 'departments';

    public $timestamps = true;

    protected $fillable = ['school_id', 'name'];

    public static function get_teachers($deparment_id) {
        $classes = self::get_classes($deparment_id);

        $teachers = array();
        foreach ($classes as $class) {
            if (!isset($teachers[$class->teacher_id])) {
                $teachers[$class->teacher_id] = User::find($class->teacher_id);
            }
        }
        return $teachers;
    }

    /**
     * Get a department by name
     * @param string $name the name to find
     * @param boolean $casesensitive with case sensitive
     */
    public static function get_by_name($name, $school_id=null, $casesensitive=false) {
        $query = self::where('name', $casesensitive ? '=' : 'like', $name);
        if ($school_id) {
            $query->where('school_id', $school_id);
        }
        return $query->get();
    }

    /**
     * Departments belong to a School
     */
    public function school() {
        return $this->belongsTo('School');
    }

    /**
     * Departments have many classes
     */
    public function classes() {
        return $this->hasMany('aClass')->orderBy('name');
    }

    /**
     * Department can have one or more heads.
     * @return mixed
     */
    public function heads() {
        return $this->hasMany('User')->where('department_id', $this->id)->where('role', User::DEPARTMENT_HEAD);
    }

    /**
     * Departments have many teachers, which belong to the department's classes
     */
    public function teachers() {

        $teachers = array();

        foreach (aClass::where('department_id', $this->id)->get() as $class) {
            foreach (User::where('id', $class->teacher_id)->get() as $teacher) {
                $teachers[$teacher->id] = $teacher;
            }
        }

        uasort($teachers, function($a,$b) {
            return strcmp($a->name, $b->name);
        });

        return $teachers;
    }
}