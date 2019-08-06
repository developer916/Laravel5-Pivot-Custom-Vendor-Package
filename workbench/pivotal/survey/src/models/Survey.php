<?php namespace Pivotal\Survey\Models;

use \Config;
use Illuminate\Database\Eloquent\Model;
use Pivotal\Course\Models\Course;
use Pivotal\Cycle\Models\Cycle;
use Pivotal\Survey\Models\Collections\SurveyCollection;
use Pivotal\Survey\Models\Relations\SurveyCourseRelation;
use Pivotal\Survey\Models\Relations\SurveyCycleRelation;
use Pivotal\Survey\Models\Relations\SurveyResponseRelation;
use Pivotal\Survey\Models\SurveyInterface;

class Survey extends Model implements SurveyInterface
{
    protected $connection = 'limemysql';
    protected $table = 'surveys';
    protected $primaryKey = 'sid';

    public function __construct($attributes = array())
    {
        $this->table = Config::get('limesurvey.db_prefix').$this->table;
        parent::__construct($attributes);
    }

    public function questions()
    {
        return $this->HasMany('Pivotal\Survey\Models\Question','sid','sid');
    }

    public function responses()
    {
        $instance = new Response;
        $instance->setSid($this->sid);
        return new SurveyResponseRelation($instance->newQuery(),$this);
    }

    public function cycle()
    {
        $instance = new Cycle();
        return new SurveyCycleRelation($instance->newQuery(),$this);
    }

    public function course()
    {
        $instance = new Course();
        return new SurveyCourseRelation($instance->newQuery(),$this);
    }

    public function newCollection(array $models = [])
    {
        return new SurveyCollection($models);
    }

}