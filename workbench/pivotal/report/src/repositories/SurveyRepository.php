<?php namespace Pivotal\Report\Repositories;

use Pivotal\Cycle\Models\CycleInterface;
use Pivotal\Cycle\Repositories\CycleRepositoryInterface;
use Pivotal\School\Models\SchoolInterface;
use Pivotal\Survey\Models\SurveyInterface;
use Pivotal\Survey\Repositories\SurveyRepositoryInterface;

class SurveyRepository implements SurveyRepositoryInterface, SurveyInterface
{
    private $entity;
    private $collection;

    /**
     * @param SurveyInterface $model
     */
    public function __construct(SurveyInterface $model, SurveyCollectionRepositoryInterface $collection)
    {
        $this->setEntity($model);
        $this->setCollection($collection);
    }

    /**
     * @param SurveyInterface $model
     */
    public function setEntity(SurveyInterface $model)
    {
        $this->entity = $model;
    }

    /**
     * @return SurveyInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param SurveyCollectionRepositoryInterface $collection
     */
    public function setCollection(SurveyCollectionRepositoryInterface $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return ReportRepositoryInterface
     */
    public function getCollection()
    {
        return $this->collection;
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