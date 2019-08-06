<?php namespace Pivotal\School\Repositories;

use Pivotal\School\Models\SchoolInterface;

class SchoolRepository implements SchoolRepositoryInterface
{
    private $entity;

    /**
     * @param SchoolInterface $model
     */
    public function __construct(SchoolInterface $model)
    {
        $this->setEntity($model);
    }

    /**
     * @param SchoolInterface $model
     */
    public function setEntity(SchoolInterface $model)
    {
        $this->entity = $model;
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
        if (property_exists($this->getEntity(), $name)) {
            return $this->getEntity()->{$name};
        }
        return null;
    }
}