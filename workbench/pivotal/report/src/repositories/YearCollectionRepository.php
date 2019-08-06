<?php namespace Pivotal\Report\Repositories;

use \DateTime;
use Closure;
use Countable;
use ArrayAccess;
use ArrayIterator;
use CachingIterator;
use Illuminate\Database\Eloquent\Collection;
use JsonSerializable;
use IteratorAggregate;
use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\ArrayableInterface;

use Pivotal\Course\Models\Collections\CourseCollectionInterface;
use Pivotal\Course\Models\Course;
use Pivotal\Survey\Models\Collections\SurveyCollection;


class YearCollectionRepository extends Collection implements YearCollectionRepositoryInterface, HasCoursesInterface, ArrayAccess, ArrayableInterface, Countable, IteratorAggregate, JsonableInterface, JsonSerializable
{
    private $entity;
    private $parent;

    /**
     * @param CycleCollectionInterface $collection
     */
    public function __construct($years = array(), SurveyCollectionRepositoryInterface $parent)
    {

        $this->setParent($parent);

        foreach ($years as $key => $year) {

            $yearRepository = new YearRepository();
            $yearRepository->id = $year;

            $yearSurveys = [];
            $currentSurveys = [];

            $yearSurveys = $parent->filter(function ($survey) use($year){
                return $survey->course->year_level == $year;
            });

            $teacher_ids = [];

            foreach($yearSurveys as $item) {
                $teacher_ids[$item->course->teacher->id] = 1;
            }

            $newYearSurveyCollection = new SurveyCollectionRepository($yearSurveys, $this->getParent()->getParent());
            $yearRepository->surveys = $newYearSurveyCollection;
            if (count($teacher_ids) >= \Utils::$year_teacher_threshold) {
                $yearRepository->teacher_threshold = true;
            } else {
                $yearRepository->teacher_threshold = false;
            }
            $this->add($yearRepository);
        }
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

}