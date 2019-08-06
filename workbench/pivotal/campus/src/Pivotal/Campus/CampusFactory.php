<?php namespace Pivotal\Campus;

use Pivotal\Campus\Repositories\CampusRepositoryInterface;

class CampusFactory
{

    private $repository;

    /**
     * @param CampusRepositoryInterface $repository
     */
    public function __construct(CampusRepositoryInterface $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * @param CampusRepositoryInterface $repository
     * @return $this
     */
    public function setRepository(CampusRepositoryInterface $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return CampusRepositoryInterface
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