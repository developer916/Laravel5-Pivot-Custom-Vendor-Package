<?php namespace Pivotal\Report\Repositories;

use Pivotal\Course\Models\Collections\CourseCollectionInterface;
use Pivotal\Course\Repositories\CourseRepositoryInterface;
use Pivotal\Cycle\Models\CycleInterface;
use Pivotal\Cycle\Repositories\CycleRepositoryInterface;
use Pivotal\Models\CourseInterface;
use Pivotal\School\Models\SchoolInterface;

class CourseRepository implements CycleRepositoryInterface
{
    private $entity;
    private $parent;

    /**
     * @param CycleInterface $model
     */
    public function __construct(CourseInterface $model, CourseCollectionRepositoryInterface $parent)
    {
        $this->setEntity($model);
        $this->setParent($parent);
    }

    public function surveys()
    {
        if (!isset($this->surveys)) {
            $cycles = [];
            array_push($cycles, $this->cycles()->current()->id);
            if ($previous = $this->cycles()->previous()) {
                array_push($cycles, $previous->id);
            }
            $surveys = $this->getEntity()->surveys()->get()->filter(function ($survey) use ($cycles) {
                foreach ($cycles as $cycle) {
                    if ($cycle == $survey->cycle->id) {
                        return true;
                    }
                }
            });
            $this->surveys = new SurveyCollectionRepository($surveys, $this);
        }
        return $this->surveys;
    }


    /**
     * @param CourseInterface $model
     */
    public function setEntity(CourseInterface $model)
    {
        $this->entity = $model;
    }

    /**
     * @return CourseInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param CourseCollectionRepositoryInterface $parent
     */
    public function setParent(CourseCollectionRepositoryInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return CourseCollectionRepositoryInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->getEntity(), $method), $args);

    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::getEntity(), $method), $args);
    }

    public function __get($name)
    {
        return $this->getEntity()->{$name};

    }


}