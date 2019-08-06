<?php namespace Pivotal\School\Models;

use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Pivotal\School\Models\SchoolInterface;
use Pivotal\Survey\Models\Relations\SchoolSurveyRelation;
use Pivotal\Survey\Models\Survey;
use Pivotal\User\Models\User;

class School extends Eloquent implements SchoolInterface, StaplerableInterface
{
    use EloquentTrait;

    protected $table = 'schools';

    protected $guarded = array('id');

    public $timestamps = true;

    protected $fillable = ['name', 'abbr', 'logo', 'is_campused'];

    public function __construct(array $attributes = array()) {

        $this->hasAttachedFile('logo', [ 'styles' =>
        // constrain the dimensions of the logo
            [ 'medium' => 'x100', 'thumb'  => 'x75']
        ]);

        parent::__construct($attributes);
    }

    /**
     * Schools have many departments
     */
    public function departments() {
        return $this->hasMany('Department')->orderBy('name');
    }

    /**
     * Schools have many classes, which belong to the school's departments
     */
    public function classes() {
        return $this->hasManyThrough('aClass', 'Department')->orderBy('name');
    }

    /**
     * Schools have many classes, which belong to the school's departments
     */
    public function classesForYear($year = false) {
        if (!$year) {
            $year = date('Y');
        }
        return $this->classes()->where('start_year', $year)->get();
    }

    /**
     * School has many campuses
     */
    public function campuses() {
        return $this->hasMany('Campus');
    }

    /**
     * Schools have many teachers
     */
    public function teachers() {
        return $this->hasMany('User')->orderBy('name');
    }
    /**
     * Schools have one admin
     */
    public function admin() {
        return $this->teachers()->where('role', '=', User::SCHOOL_ADMIN)->first();
    }

    /**
     * Schools have many cycles
     */
    public function cycles() {
        return $this->hasMany('Cycle')->orderBy('start_date', 'DESC');
    }

    /**
     * Schools have many cycles
     */
    public function cyclesByDate() {
        return $this->hasMany('Cycle')->orderBy('start_date', 'DESC');
    }

    /**
     * Schools have many cycles
     */
    public function cyclesByName() {
        return $this->hasMany('Cycle')->orderBy('name');
    }

    /**
     * Schools have many cycles
     */
    public function lastCycle() {
        return $this->hasMany('Cycle')->orderBy('created_at', 'DESC')->limit(1);
    }

    public function surveys()
    {
        $instance = new Survey();
        return new SchoolSurveyRelation($instance->newQuery(),$this);
    }


}