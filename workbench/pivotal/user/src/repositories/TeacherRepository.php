<?php namespace Pivotal\User\Repositories;

use Illuminate\Auth\UserInterface;
use Pivotal\User\Repositories\UserRepositoryInterface;


class TeacherRepository implements UserRepositoryInterface
{
    private $entity;

    /**
     * @param UserInterface $model
     */
    public function __construct(UserInterface $model)
    {
        $this->setEntity($model);
    }

    /**
     * @param UserInterface $model
     */
    public function setEntity(UserInterface $model)
    {
        $this->entity = $model;
    }

    /**
     * @return UserInterface
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