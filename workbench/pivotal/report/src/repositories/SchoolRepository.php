<?php namespace Pivotal\Report\Repositories;

use Pivotal\School\Models\SchoolInterface;
use Pivotal\School\Repositories\SchoolRepositoryInterface;

class SchoolRepository implements SchoolRepositoryInterface, HasSurveysInterface
{
    private $entity;
    private $report;

    /**
     * @param SchoolInterface $model
     */
    public function __construct(SchoolInterface $model, ReportRepositoryInterface $report)
    {
        $this->setEntity($model);
        $this->setReport($report);
    }

    /**
     * @param SchoolInterface $model
     */
    public function setEntity(SchoolInterface $model)
    {
        $this->entity = $model;
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

    /**
     * @return SchoolInterface
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
            return $this->getEntity()->{$name};
    }


    public function surveys()
    {
        if (!isset($this->surveys)) {
            $cycles = [];
            array_push($cycles, $this->cycles()->current()->id);
            if ($previous = $this->cycles()->previous()) {
                array_push($cycles, $previous->id);
            }
            $surveys = $this->getEntity()->surveys()->get()->filter(function ($survey) use ($cycles) {
                foreach ($cycles as $cycle) {
                    if ($cycle == $survey->cycle->id) {
                        return true;
                    }
                }
            });

            $this->surveys = new SurveyCollectionRepository($surveys, $this);
        }
        return $this->surveys;
    }


    public function cycles()
    {
        if(!isset($this->cycles))
        {
            $this->cycles = new CycleCollectionRepository($this->getEntity()->cycles()->get(),$this->getReport());
        }
        return $this->cycles;
    }


}