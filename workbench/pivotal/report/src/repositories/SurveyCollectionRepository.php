<?php namespace Pivotal\Report\Repositories;

use Carbon\Carbon;
use \DateTime;
use Closure;
use Countable;
use ArrayAccess;
use ArrayIterator;
use CachingIterator;
use JsonSerializable;
use IteratorAggregate;
use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\ArrayableInterface;

use Pivotal\Course\Models\Course;
use Pivotal\Cycle\Models\Collections\CycleCollection;
use Pivotal\Cycle\Models\Collections\CycleCollectionInterface;
use Pivotal\Cycle\Models\CycleInterface;
use Pivotal\Survey\Models\Collections\ResponseCollection;
use Pivotal\Survey\Models\Collections\SurveyCollection;
use Pivotal\Survey\Models\Collections\SurveyCollectionInterface;
use Pivotal\Survey\Models\SurveyInterface;


class SurveyCollectionRepository implements SurveyCollectionRepositoryInterface, HasDepartmentsInterface, ArrayAccess, ArrayableInterface, Countable, IteratorAggregate, JsonableInterface, JsonSerializable
{
    private $entity;
    private $parent;

    /**
     * @param CycleCollectionInterface $collection
     */
    public function __construct(SurveyCollectionInterface $collection, HasSurveysInterface $parent)
    {
        //Build a new collection with repository entities instead of physical ones
        $newCollection = new SurveyCollection();
        foreach ($collection as $survey) {
            $newSurvey = new SurveyRepository($survey, $this);
            $newCollection->add($newSurvey);
        }
        $this->setEntity($newCollection);
        $this->setParent($parent);
    }

    /**
     * Set the items on this collection by getting the most recent cycle of the parent and setting its surveys
     */
    public function current()
    {
        $collection = $this->filter(function ($survey) {
            return $survey->cycle->id == $this->getParent()->cycles()->current()->id;
        });
        return new SurveyCollectionRepository($collection, $this->parent);
    }

    /**
     * Set the items on this collection by getting the most recent cycle of the parent and setting its surveys
     */
    public function filterSameCurrentYear()
    {
        $date = $this->getParent()->cycles()->current()->start_date;
        $date = Carbon::createFromFormat('d/m/Y', $date);
        $currentSurveyYear = $date->year;

        $collection = $this->filter(function ($survey) use ($currentSurveyYear) {
            $dateSurvey = Carbon::createFromFormat('d/m/Y', $survey->cycle->start_date);
            return $dateSurvey->year == $currentSurveyYear;
        });

        return new SurveyCollectionRepository($collection, $this->parent);
    }

    /**
     * Set the items on this collection by getting the second to most recent cycle of the parent and setting its surveys
     */
    public function previous()
    {
        if ($this->getParent()->cycles()->previous()) {
            $collection = $this->filter(function ($survey) {
                return $survey->cycle->id == $this->getParent()->cycles()->previous()->id;
            });
            return new SurveyCollectionRepository($collection, $this->parent);
        }

        return null;

    }

    public function byDepartment()
    {

        $department_collection = new DepartmentCollectionRepository($this->getParent()->departments, $this);

        foreach ($department_collection as $department) {
            foreach ($this as $survey) {
                $survey->filter_department = $department;
            }


            $collection = $this->filter(function ($survey) {
                return $survey->course->department->id == $survey->filter_department->id;
            });

            //Make sure the deparment has enough unique teachers with responses to meet the threshold
            $teacher_ids = [];
            foreach ($collection as $item) {
                $teacher_ids[$item->course->teacher->id] = 1;
                if (count($teacher_ids) >= \Utils::$department_teacher_threshold) {
                    $newCollection = new SurveyCollectionRepository($collection, $this->parent);
                    break;
                }
            }
            if (!isset($newCollection)) {
                $newCollection = new SurveyCollectionRepository(new SurveyCollection(), $this->parent);
            }

            $department->surveys = $newCollection;
        }
        return $department_collection;
    }

    public function byYear()
    {
        $years = Course::select('year_level')->groupBy('year_level')->lists('year_level');

        $year_collection = new YearCollectionRepository($years, $this);

        return $year_collection;
    }


    /**
     * separate the related surveys by course to a course collection
     * @return CourseCollectionRepository
     */
    public function byCourse()
    {
        $course_collection = new CourseCollectionRepository($this->getParent()->courses, $this);

        foreach ($course_collection as $course) {
            foreach ($this as $survey) {
                $survey->filter_course = $course;
            }

            $collection = $this->filter(function ($survey) {
                return $survey->course->id == $survey->filter_course->id;
            });

            $newCollection = new SurveyCollectionRepository($collection, $this->parent);

            $course->surveys = $newCollection;
        }
        return $course_collection;
    }

    public function responses()
    {
        $new_responses = [];
        foreach ($this as $survey) {
            foreach ($survey->responses as $response) {
                $new_responses[] = $response;
            }
        }
        $responseCollection = new ResponseCollection($new_responses);
        $responseCollectionRepository = new ResponseCollectionRepository($responseCollection, $this);

        return $responseCollectionRepository;
    }

    public function responsesInSids($sids)
    {
        $new_responses = [];
        foreach ($this as $survey) {
            foreach ($survey->responses as $response) {
                if (!in_array($response->sid, $sids)) {
                    continue;
                }
                $new_responses[] = $response;
            }
        }
        $responseCollection = new ResponseCollection($new_responses);
        $responseCollectionRepository = new ResponseCollectionRepository($responseCollection, $this);

        return $responseCollectionRepository;
    }


    /**
     * @param SurveyCollectionInterface $model
     */
    public function setEntity(SurveyCollectionInterface $model)
    {
        $this->entity = $model;
    }

    /**
     * @return CycleCollectionInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param HasSurveysInterface $parent
     */
    public function setParent(HasSurveysInterface $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return ReportRepositoryInterface
     */
    public function getParent()
    {
        return $this->parent;
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

    public function get($key, $default = null)
    {
        return $this->getEntity()->get($key, $default);
    }

    public function offsetGet($key)
    {
        return $this->getEntity()->offsetGet($key);
    }

    public function offsetSet($key, $value)
    {
        return $this->getEntity()->offsetSet($key, $value);
    }

    public function offsetExists($key)
    {
        return $this->getEntity()->offsetExists($key);
    }

    public function offsetUnset($key)
    {
        return $this->getEntity()->offsetUnset($key);
    }

    public function toArray()
    {
        return $this->getEntity()->toArray();
    }

    public function count()
    {
        return $this->getEntity()->count();
    }

    public function getIterator()
    {
        return $this->getEntity()->getIterator();
    }

    public function toJson($options = 0)
    {
        return $this->getEntity()->toJson($options);
    }

    public function jsonSerialize()
    {
        return $this->getEntity()->toArray();
    }

}