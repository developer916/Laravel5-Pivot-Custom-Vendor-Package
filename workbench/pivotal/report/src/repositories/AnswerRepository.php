<?php namespace Pivotal\Report\Repositories;

use Pivotal\School\Models\SchoolInterface;
use Pivotal\School\Repositories\SchoolRepositoryInterface;
use Pivotal\Survey\Models\AnswerInterface;

class AnswerRepository implements AnswerRepositoryInterface
{
    private $entity;
    private $report;

    /**
     * @param SchoolInterface $model
     */
    public function __construct(AnswerInterface $model, AnswerCollectionRepositoryInterface $collection)
    {
        $this->setEntity($model);
        $this->setCollection($collection);
    }

    /**
     * @param AnswerInterface $model
     */
    public function setEntity(AnswerInterface $model)
    {
        $this->entity = $model;
    }

    /**
     * @param ReportRepositoryInterface $report
     */
    public function setCollection(AnswerCollectionRepositoryInterface $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return AnswerCollectionRepositoryInterface
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return SchoolInterface
     */
    public function getEntity()
    {
        return $this->entity;
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