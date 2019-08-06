<?php namespace Pivotal\Survey\Repositories;

use Pivotal\Survey\Models\SurveyInterface;

class SurveyRepository implements SurveyRepositoryInterface
{
    private $entity;

    /**
     * @param SurveyInterface $model
     */
    public function __construct(SurveyInterface $model)
    {
        $this->setEntity($model);
    }

    /**
     * @param SurveyInterface $model
     */
    public function setEntity(SurveyInterface $model)
    {
        $this->entity = $model;
    }

    /**
     * @return SurveyInterface
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