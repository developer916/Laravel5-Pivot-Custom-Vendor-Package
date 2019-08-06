<?php namespace Pivotal\Course;

use Pivotal\Course\Repositories\CourseRepositoryInterface;

class CourseFactory
{

    private $repository;

    /**
     * @param CourseRepositoryInterface $repository
     */
    public function __construct(CourseRepositoryInterface $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * @param CourseRepositoryInterface $repository
     * @return $this
     */
    public function setRepository(CourseRepositoryInterface $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return CourseRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }


    public function __call($method, $args)
    {
        return call_user_func_array(array($this->getRepository(), $method), $args);

    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::getRepository(), $method), $args);
    }

    public function __get($name)
    {
        if (property_exists($this->getRepository(), $name)) {
            return $this->getRepository()->{$name};
        }
    }


}