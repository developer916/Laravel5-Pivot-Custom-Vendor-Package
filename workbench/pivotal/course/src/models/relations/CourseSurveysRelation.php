<?php namespace Pivotal\Course\Models\Relations;

use Pivotal\Cycle\Models\Cycle;
use Pivotal\Models\CourseInterface;
use Pivotal\Survey\Models\Survey;
use Pivotal\Course\Models\Course;

use Illuminate\Auth\UserInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;


class CourseSurveysRelation extends Relation
{

    /**
     * Create a new relation instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @return void
     */
    public function __construct(Builder $query, CourseInterface $parent)
    {

        $survey_ids = $parent::getModel()
            ->newQuery()
            ->select('cycles_classes.limesurvey_id')
            ->join('cycles_classes', 'classes.id', '=', 'cycles_classes.class_id')
            ->where('cycles_classes.class_id','=',$parent->id)
            ->lists('limesurvey_id');

        $this->query = $query
            ->whereIn('sid', $survey_ids)
            ->groupBy('sid');

        $this->parent = $parent;
        $this->related = $query->getModel();
        $this->addConstraints();
    }


    public function addEagerConstraints(array $models)
    {
        $survey_ids = [];

        $course_ids = $this->getKeys($models, 'id');
        $survey_ids = \DB::table('cycles_classes')
                        ->select('cycles_classes.limesurvey_id')
                        ->whereIn('cycles_classes.class_id',$course_ids)
                        ->lists('limesurvey_id');

        $this->query->whereIn('sid', $survey_ids);
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