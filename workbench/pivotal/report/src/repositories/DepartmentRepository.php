<?php namespace Pivotal\Report\Repositories;


use Pivotal\Cycle\Models\CycleInterface;
use Pivotal\Department\Models\DepartmentInterface;
use Pivotal\Models\CourseInterface;


class DepartmentRepository implements DepartmentRepositoryInterface, HasSurveysInterface
{
    private $entity;
    private $parent;

    /**
     * @param DepartmentInterface $model
     */
    public function __construct(DepartmentInterface $model, HasDepartmentsInterface $parent)
    {
        $this->setEntity($model);
        $this->setParent($parent);
    }

    /**
     * @param DepartmentInterface $model
     */
    public function setEntity(DepartmentInterface $model)
    {
        $this->entity = $model;
    }

    /**
     * @return DepartmentInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param HasDepartmentsInterface $parent
     */
    public function setParent(HasDepartmentsInterface $parent)
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


    public function getReport()
    {
        return $this->parent->getReport();
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

    public function cycles()
    {
        if (!isset($this->cycles)) {
            $this->cycles = new CycleCollectionRepository($this->getEntity()->cycles()->get(), $this->getReport());
        }
        return $this->cycles;
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


}