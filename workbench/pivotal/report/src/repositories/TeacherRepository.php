<?php namespace Pivotal\Report\Repositories;

use Illuminate\Auth\UserInterface;
use Pivotal\Cycle\Models\CycleInterface;
use Pivotal\Survey\Models\Collections\SurveyCollection;
use Pivotal\Survey\Models\Collections\SurveyCollectionInterface;
use Pivotal\User\Repositories\UserRepositoryInterface;

class TeacherRepository implements TeacherRepositoryInterface, HasSurveysInterface
{
    private $entity;
    private $report;
    private $surveys;
    private $targetCycle;

    /**
     * @param UserInterface $model
     */
    public function __construct(UserInterface $model, ReportRepositoryInterface $report)
    {
        $this->setEntity($model);
        $this->setReport($report);
    }

    /**
     * @param UserInterface $model
     */
    public function setEntity(UserInterface $model)
    {
        $this->entity = $model;
    }

    /**
     * @return UserInterface
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

    public function getTargetCycle()
    {
        return $this->getReport()->getTargetCycle();
    }


    public function cycles()
    {
        if(!isset($this->cycles))
        {
            $this->cycles = new CycleCollectionRepository($this->getEntity()->cycles()->get(),$this->getReport());
        }
        return $this->cycles;
    }


    public function courses()
    {
        $collection = $this->getEntity()->courses;
        return $collection;
    }

    public function setSurveys(SurveyCollectionInterface $surveys)
    {
        if (isset($this->surveys)) {
            $this->surveys = $this->surveys->merge($surveys);
        }else{
            $this->surveys = $surveys;
        }
        return $this;
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



}