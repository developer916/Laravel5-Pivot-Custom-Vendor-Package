<?php namespace Pivotal\Report\Repositories;

use Pivotal\Cycle\Models\CycleInterface;
use Pivotal\Cycle\Repositories\CycleRepositoryInterface;
use Pivotal\School\Models\SchoolInterface;

class CycleRepository implements CycleRepositoryInterface
{
    private $entity;
    private $report;

    /**
     * @param CycleInterface $model
     */
    public function __construct(CycleInterface $model, ReportRepositoryInterface $report)
    {
        $this->setEntity($model);
        $this->setReport($report);
    }

    /**
     * @param CycleInterface $model
     */
    public function setEntity(CycleInterface $model)
    {
        $this->entity = $model;
    }

    /**
     * @return CycleInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param ReportRepositoryInterface $report
     */
    public function setReport(ReportRepositoryInterface $report)
    {
        $this->report = $report;
    }

    /**
     * @return ReportRepositoryInterface
     */
    public function getReport()
    {
        return $this->report;
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
        return $this->getEntity()->{$name};
    }


}