<?php namespace Pivotal\Department;

use Pivotal\Department\Repositories\DepartmentRepositoryInterface;

class DepartmentFactory
{
    private $repository;

    /**
     * @param DepartmentRepositoryInterface $repository
     */
    public function __construct( DepartmentRepositoryInterface $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * @param  DepartmentRepositoryInterface $repository
     * @return $this
     */
    public function setRepository( DepartmentRepositoryInterface $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return  DepartmentRepositoryInterface
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
        return null;
    }
}