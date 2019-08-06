<?php namespace Pivotal\Report\Repositories;

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
use Pivotal\Course\Models\Collections\CourseCollection;
use Pivotal\Course\Models\Collections\CourseCollectionInterface;



class CourseCollectionRepository implements CourseCollectionRepositoryInterface, ArrayAccess, ArrayableInterface, Countable, IteratorAggregate, JsonableInterface, JsonSerializable
{
    private $entity;
    private $parent;

    /**
     * @param CourseCollectionInterface $collection
     */
    public function __construct(CourseCollectionInterface $collection, SurveyCollectionRepositoryInterface $parent)
    {
        //Build a new collection with repository entities instead of physical ones
        $newCollection = new CourseCollection();
        foreach ($collection as $course) {
            $newCourse = new CourseRepository($course, $this);
            $newCollection->add($newCourse);
        }
        $this->setEntity($newCollection);
        $this->setParent($parent);
    }

    /**
     * @param CourseCollectionInterface $collection
     */
    public function setEntity(CourseCollectionInterface $collection)
    {
        $this->entity = $collection;
    }

    /**
     * @return CourseCollectionInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param SurveyCollectionRepositoryInterface $report
     */
    public function setParent(SurveyCollectionRepositoryInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return SurveyCollectionRepositoryInterface
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