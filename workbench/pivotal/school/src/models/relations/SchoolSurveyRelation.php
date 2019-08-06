<?php namespace Pivotal\Survey\Models\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Pivotal\School\Models\School;
use Pivotal\School\Models\SchoolInterface;
use Pivotal\Survey\Models\ResponseInterface;
use Pivotal\Survey\Models\SurveyInterface;
use \Config;

class SchoolSurveyRelation extends Relation
{

    /**
     * Create a new relation instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @return void
     */
    public function __construct(Builder $query, SchoolInterface $parent)
    {
        $survey_ids = School::getModel()
            ->newQuery()
            ->select('cycles_classes.*')
            ->join('cycles','schools.id','=','cycles.school_id')
            ->join('cycles_classes','cycles.id','=','cycles_classes.cycle_id')
            ->where('schools.id','=',$parent->id)
            ->lists('limesurvey_id');

        $this->query = $query
            ->whereIn(Config::get('limesurvey.db_prefix').'surveys.sid',$survey_ids);



        $this->parent = $parent;
        $this->related = $query->getModel();

        $this->addConstraints();
    }


    public function addEagerConstraints(array $models)
    {


    }

    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model)
        {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    public function addConstraints()
    {

    }

    public function match(array $models, Collection $results, $relation)
    {
        $value = $this->related->newCollection($models);


        foreach($models as $model)
        {
            $model->setRelation($relation, $value);

        }
        return $models;
    }

    public function getResults()
    {
        $results = $this->query->get();
        return $results;
    }

}