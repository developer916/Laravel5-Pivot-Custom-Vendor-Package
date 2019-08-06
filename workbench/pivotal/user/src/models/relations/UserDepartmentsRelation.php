<?php namespace Pivotal\User\Models\Relations;

use Pivotal\Department\Models\Department;
use Pivotal\Course\Models\Course;

use Illuminate\Auth\UserInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;


class UserDepartmentsRelation extends Relation
{

    /**
     * Create a new relation instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @return void
     */
    public function __construct(Builder $query, UserInterface $parent)
    {
        $course = Course::getModel();
        $this->query = $query
            ->select($query->getModel()->getTable() . ".*")
            ->join($course->getTable(),'departments.id','=','classes.department_id')
            ->where('classes.teacher_id','=',$parent->id)
            ->groupBy('departments.id');

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