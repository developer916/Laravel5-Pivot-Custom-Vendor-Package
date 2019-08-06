<?php namespace Pivotal\Survey\Models\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Pivotal\Survey\Models\SurveyInterface;

class SurveyCycleRelation extends Relation
{

    /**
     * Create a new relation instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @return void
     */
    public function __construct(Builder $query, SurveyInterface $parent)
    {
        $this->query = $query
            ->select('cycles.*')
            ->join('cycles_classes','cycles_classes.cycle_id','=','cycles.id')
            ->where('cycles_classes.limesurvey_id','=',$parent->sid);

        $this->query = $query;
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

        $results = $this->query->first();
        return $results;
    }

}