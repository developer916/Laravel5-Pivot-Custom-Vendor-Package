<?php namespace Pivotal\Repositories;

use Pivotal\Course\Repositories\CourseRepositoryInterface;
use Pivotal\Models\CourseInterface;

class CourseRepository implements CourseRepositoryInterface
{
    private $entity;

    /**
     * @param CourseInterface $model
     */
    public function __construct(CourseInterface $model)
    {
        $this->setEntity($model);
    }

    /**
     * @param CourseInterface $model
     */
    public function setEntity(CourseInterface $model)
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



