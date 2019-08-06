<?php namespace Pivotal\User\Models\Relations;

use Pivotal\Cycle\Models\Cycle;
use Pivotal\Course\Models\Course;

use Illuminate\Auth\UserInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;


class UserCyclesRelation extends Relation
{

    /**
     * Create a new relation instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  \Illuminate\Database\Eloquent\Model $parent
     * @return void
     */
    public function __construct(Builder $query, UserInterface $parent)
    {
        $this->query = $query
            ->select('cycles.*')
            ->join('cycles_classes', 'cycles.id', '=', 'cycles_classes.cycle_id')
            ->join('classes', 'classes.id', '=', 'cycles_classes.class_id')
            ->where('classes.teacher_id', '=', $parent->id)
            ->groupBy('cycles.id');

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