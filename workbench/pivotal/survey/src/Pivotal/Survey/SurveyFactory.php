<?php namespace Pivotal\Survey;


use Pivotal\Survey\Repositories\SurveyRepositoryInterface;

class SurveyFactory
{
    private $repository;

    /**
     * @param SurveyRepositoryInterface $repository
     */
    public function __construct(SurveyRepositoryInterface $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * @param  SurveyRepositoryInterface $repository
     * @return $this
     */
    public function setRepository(SurveyRepositoryInterface $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return  SurveyRepositoryInterface
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