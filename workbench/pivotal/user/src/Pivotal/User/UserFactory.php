<?php namespace Pivotal\User;

use Pivotal\User\Repositories\UserRepositoryInterface;

class UserFactory
{
    private $repository;

    /**
     * @param UserRepositoryInterface
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * @param  UserRepositoryInterface $repository
     * @return $this
     */
    public function setRepository(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return  UserRepositoryInterface
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