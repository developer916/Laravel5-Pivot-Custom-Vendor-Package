<?php namespace Pivotal\Cycle\Repositories;

use Pivotal\Cycle\Models\CycleInterface;

class CycleRepository implements CycleRepositoryInterface
{
    private $entity;

    /**
     * @param CycleInterface $model
     */
    public function __construct(CycleInterface $model)
    {
        $this->setEntity($model);
    }

    /**
     * @param CycleInterface $model
     */
    public function setEntity(CycleInterface $model)
    {
        $this->entity = $model;
    }

    /**
     * @return CycleInterface
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
    }
}