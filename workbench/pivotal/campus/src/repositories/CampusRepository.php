<?php namespace Pivotal\Campus\Repositories;

use Pivotal\Campus\Repositories\CampusRepositoryInterface;
use Pivotal\Campus\Models\CampusInterface;

class CampusRepository implements CampusRepositoryInterface
{
    private $entity;

    /**
     * @param CampusInterface $model
     */
    public function __construct(CampusInterface $model)
    {
        $this->setEntity($model);
    }

    /**
     * @param CampusInterface $model
     */
    public function setEntity(CampusInterface $model)
    {
        $this->entity = $model;
    }

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



