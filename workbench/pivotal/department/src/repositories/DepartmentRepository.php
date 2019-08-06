<?php namespace Pivotal\Department\Repositories;

use Pivotal\Department\Models\DepartmentInterface;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    private $entity;

    /**
     * @param DepartmentInterface $model
     */
    public function __construct(DepartmentInterface $model)
    {
        $this->setEntity($model);
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