<?php namespace Pivotal\Department\Models\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Pivotal\Department\Models\DepartmentInterface;
use Pivotal\School\Models\School;
use Pivotal\School\Models\SchoolInterface;
use Pivotal\Survey\Models\ResponseInterface;
use Pivotal\Survey\Models\SurveyInterface;
use \Config;

class DepartmentSurveyRelation extends Relation
{

    /**
     * Create a new relation instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @return void
     */
    public function __construct(Builder $query, DepartmentInterface $parent)
    {
        $course_ids = [];
        foreach($parent->courses as $course)
        {
         $course_ids[] = $course->id;
        }

        $survey_ids = \DB::table('cycles_classes')
            ->select('cycles_classes.limesurvey_id')
            ->whereIn('cycles_classes.class_id',$course_ids)
            ->lists('limesurvey_id');

        $this->query = $query
            ->whereIn(Config::get('limesurvey.db_prefix').'surveys.sid',$survey_ids);

        $this->parent = $parent;
        $this->related = $query->getModel();

        $this->addConstraints();
    }


    public function addEagerConstraints(array $models)
    {
        parent::addEagerConstraints($models);
    }

    public function initRelation(array $models, $relation)
    {

    }

    public function addConstraints()
    {

    }

    public function match(array $models, Collection $results, $relation)
    {

    }

    public function getResults()
    {
        $results = $this->query->get();
        return $results;
    }

}