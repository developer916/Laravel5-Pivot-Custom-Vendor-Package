<?php namespace Pivotal\Course\Models;

use Pivotal\Course\Models\Collections\CourseCollection;
use Pivotal\Cycle\Models\Cycle;
use Pivotal\Survey\Models\Survey;
use Codesleeve\Stapler\ORM\EloquentTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Pivotal\Course\Models\Relations\CourseSurveysRelation;
use Pivotal\Models\CourseInterface;

class Course extends Eloquent implements CourseInterface
{
    use EloquentTrait;

    /**
     * @var string
     */
    protected $table = 'classes';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $fillable = ['department_id', 'teacher_id', 'name', 'code', 'year_level', 'num_students', 'start_year'];

    /**
     * Classes belong to a department
     */
    public function department() {
        return $this->belongsTo('Department');
    }

    /**
     * Classes belong to a teacher
     */
    public function teacher() {
        return $this->belongsTo('User', 'teacher_id');
    }

    /**
     * Classes have many cycles
     */
    public function cycles() {
        return $this->belongsToMany('Cycle', 'cycles_classes', 'class_id', 'cycle_id')
            ->withPivot('limesurvey_id', 'url', 'adminurl')->orderBy('name');
    }

    /**
     * Classes belong to a Campus
     */
    public function campus() {
        return $this->belongsTo('Campus');
    }

    public function surveys()
    {
        $related = new Survey();
        $parent = $this;
        return new CourseSurveysRelation($related->newQuery(), $this);
    }

    public function getLimesurveyId(Cycle $cycle)
    {
        $limesurveyId = \DB::table($this->table)
            ->select('cycles_classes.limesurvey_id')
            ->join('cycles_classes', function($join) use ($cycle) {
                $join->on('cycles_classes.class_id', '=', 'classes.id');
            })->where('cycles_classes.cycle_id', $cycle->id)
            ->where('classes.id', $this->id)
            ->first();
        if (!$limesurveyId) {
            return false;
        }

        return $limesurveyId->limesurvey_id;
    }

    public function getLimesurveyIds(Cycle $cycle)
    {
        $limesurveyIds = \DB::table($this->table)
            ->select('cycles_classes.limesurvey_id')
            ->join('cycles_classes', function($join) use ($cycle) {
                $join->on('cycles_classes.class_id', '=', 'classes.id');
            })->where('cycles_classes.cycle_id', $cycle->id)
            ->get();
        if (!$limesurveyIds) {
            return false;
        }
        if (count($limesurveyIds) == 0) {
            return false;
        }
        $result = [];
        foreach ($limesurveyIds as $id) {
            $result[] = $id->limesurvey_id;
        }

        return $result;
    }

    public function newCollection(array $models = [])
    {
        return new CourseCollection($models);
    }

    public function getNameAttribute($value)
    {
        return $value.' - '.$this->start_year;
    }




}