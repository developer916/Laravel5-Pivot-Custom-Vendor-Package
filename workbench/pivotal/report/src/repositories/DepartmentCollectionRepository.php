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

use Pivotal\Cycle\Models\Collections\CycleCollection;
use Pivotal\Cycle\Models\Collections\CycleCollectionInterface;
use Pivotal\Department\Models\Collections\DepartmentCollection;
use Pivotal\Cycle\Models\CycleInterface;
use Pivotal\Department\Models\Collections\DepartmentCollectionInterface;


class DepartmentCollectionRepository implements DepartmentCollectionRepositoryInterface, HasDepartmentsInterface, ArrayAccess, ArrayableInterface, Countable, IteratorAggregate, JsonableInterface, JsonSerializable
{
    private $entity;
    private $report;

    /**
     * @param CycleCollectionInterface $collection
     */
    public function __construct(DepartmentCollectionInterface $collection, HasDepartmentsInterface $parent)
    {

        //Build a new collection with repository entities instead of physical ones
        $newCollection = new DepartmentCollection();
        foreach ($collection as $department) {
            $newDepartment = new DepartmentRepository($department, $this);
            $newCollection->add($newDepartment);
        }
        $this->setEntity($newCollection);
        $this->setParent($parent);

    }


    /**
     * @param CycleCollectionInterface $collection
     */
    public function setEntity(DepartmentCollectionInterface $collection)
    {
        $this->entity = $collection;
    }

    /**
     * @return DepartmentCollectionInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param HasDepartmentsInterface $parent
     */
    public function setParent(HasDepartmentsInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return HasDepartmentsInterface
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