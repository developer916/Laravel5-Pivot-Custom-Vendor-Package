<?php namespace Pivotal\School;

use Pivotal\School\Repositories\SchoolRepositoryInterface;

class SchoolFactory
{
    private $repository;

    /**
     * @param SchoolRepositoryInterface $repository
     */
    public function __construct(SchoolRepositoryInterface $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * @param  SchoolRepositoryInterface $repository
     * @return $this
     */
    public function setRepository(SchoolRepositoryInterface $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return  SchoolRepositoryInterface
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