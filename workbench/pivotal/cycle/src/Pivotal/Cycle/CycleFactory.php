<?php namespace Pivotal\Cycle;

use Pivotal\Cycle\Repositories\CycleRepositoryInterface;

class CycleFactory
{
    private $repository;

    /**
     * @param CycleRepositoryInterfacee $repository
     */
    public function __construct(CycleRepositoryInterface $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * @param CycleRepositoryInterface $repository
     * @return $this
     */
    public function setRepository(CycleRepositoryInterface $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return CycleRepositoryInterface
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