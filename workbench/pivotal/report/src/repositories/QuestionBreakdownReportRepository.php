<?php namespace Pivotal\Report\Repositories;

use Illuminate\Auth\UserInterface;
use Pivotal\Course\Models\Collections\CourseCollectionInterface;
use Pivotal\Cycle\Models\Collections\CycleCollectionInterface;
use Pivotal\Cycle\Models\CycleInterface;
use Pivotal\Cycle\Repositories\CycleRepositoryInterface;
use Pivotal\Department\Models\DepartmentInterface;
use Pivotal\Models\ReportInterface;
use Pivotal\Report\Models\Report;
use Pivotal\School\Models\SchoolInterface;

class QuestionBreakdownReportRepository implements ReportRepositoryInterface, HasDepartmentsInterface
{
    private $teacher;
    private $department;
    private $school;
    private $targetCycle;
    private $cycles = array();
    private $entity;
    private $key;


    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setData($data)
    {
        $this->getEntity()->data = serialize($data);
    }

    /**
     * @param null $key
     * @return array
     */
    public function getData()
    {
        return unserialize($this->getEntity()->data);
    }

    /**
     * @param UserInterface $teacher
     * @return $this
     */
    public function setTeacher(UserInterface $teacher)
    {
        $this->teacher = new TeacherRepository($teacher, $this);
        $this->setSchool($this->teacher->school);
        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * @param DepartmentInterface $department
     */
    public function setDepartment(DepartmentInterface $department)
    {
        $this->department = new DepartmentRepository($department,$this);
        $this->setSchool($department->school);
        return $this;
    }

    /**
     * @return DepartmentInterface
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school)
    {
        $this->school = new SchoolRepository($school, $this);
        return $this;
    }

    /**
     * @return SchoolInterface
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param CycleInterface $cycle
     */
    public function setTargetCycle(CycleInterface $cycle)
    {
        $newCycle = new CycleRepository($cycle,$this);
        $this->targetCycle = $newCycle;
        return $this;
    }

    /**
     * @return CycleInterface
     */
    public function getTargetCycle()
    {
        if(!isset($this->targetCycle))
        {
            return false;
        }
        return $this->targetCycle;
    }

    /**
     * @param ReportInterface $report
     */
    public function setEntity(ReportInterface $report)
    {
        $this->entity = $report;
        return $this;
    }

    public function getReport()
    {
        return $this;
    }


    public function getEntity()
    {
        return $this->entity;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function load($key)
    {
        $this->setKey($key);

        $matching = Report::where('school_id','=',$this->getSchool()->id)->where('key','=',$key);

        //Find a matching report in db
        if ($department = $this->getDepartment()) {
            $matching = $matching->where('department_id', '=', $department->id);
        }
        if ($teacher = $this->getTeacher()) {
            $matching = $matching->where('user_id', '=', $teacher->id);
        }
        if ($cycle = $this->getTargetCycle()) {
            $matching = $matching->where('cycle_id', '=', $cycle->id);
        }
        if ($key = $this->getKey()) {
            $matching = $matching->where('key', '=', $key);
        }

        if($match = $matching->first())
        {
            $this->setEntity($match);
        }else{
            $report = new Report();
            $this->setEntity($report);
            $this->buildEntityData();
        }

    }

    public function buildEntityData()
    {
        $entity = $this->getEntity();

        if ($teacher = $this->getTeacher()) {
            $entity->user_id = $teacher->id;
        }
        if ($school = $this->getSchool()) {
            $entity->school_id = $school->id;
        }
        if ($cycle = $this->getTargetCycle()) {
            $entity->cycle_id = $cycle->id;
        }
        if ($department = $this->getDepartment()) {
            $entity->department_id = $department->id;
        }
        $entity->key = $this->getKey();

        $entity->data = serialize($this->getData());
    }




    public function save()
    {
        $this->buildEntityData();
        $this->getEntity()->save();
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