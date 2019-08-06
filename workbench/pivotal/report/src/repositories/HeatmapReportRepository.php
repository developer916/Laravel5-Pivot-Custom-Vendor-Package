<?php namespace Pivotal\Report\Repositories;

use Illuminate\Auth\UserInterface;
use Pivotal\Course\Models\Collections\CourseCollectionInterface;
use Pivotal\Cycle\Models\Collections\CycleCollectionInterface;
use Pivotal\Cycle\Models\CycleInterface;
use Pivotal\Cycle\Repositories\CycleRepositoryInterface;
use Pivotal\Department\Models\DepartmentInterface;
use Pivotal\School\Models\SchoolInterface;

class HeatmapReportRepository implements ReportRepositoryInterface, HasDepartmentsInterface
{
    private $_data = array();
    private $teacher;
    private $department;
    private $school;
    private $targetCycle;
    private $cycles = array();


    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->_data[$k] = $value;
            }
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    /**
     * @param null $key
     * @return array
     */
    public function getData($key = null)
    {
        if (isset($key)) {
            if (isset($this->_data[$key])) {
                return $this->_data[$key];
            }
            return null;
        }
        return $this->_data;

    }

    /**
     * @param UserInterface $teacher
     * @return $this
     */
    public function setTeacher(UserInterface $teacher)
    {
        $this->teacher = new TeacherRepository($teacher, $this);
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

    public function setTargetCycle(CycleInterface $cycle)
    {
        $newCycle = new CycleRepository($cycle,$this);
        $this->targetCycle = $newCycle;
    }

    public function getTargetCycle()
    {
        if(!isset($this->targetCycle))
        {
            return false;
        }
        return $this->targetCycle;
    }


    public function getReport()
    {
        return $this;
    }
}