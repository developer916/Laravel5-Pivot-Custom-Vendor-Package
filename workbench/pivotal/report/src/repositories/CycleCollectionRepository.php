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
use Pivotal\Cycle\Models\CycleInterface;


class CycleCollectionRepository implements ArrayAccess, ArrayableInterface, Countable, IteratorAggregate, JsonableInterface, JsonSerializable
{
    private $entity;
    private $report;

    /**
     * @param CycleCollectionInterface $collection
     */
    public function __construct(CycleCollectionInterface $collection, ReportRepositoryInterface $report)
    {

        if ($report->getTargetCycle()) {
            foreach ($collection as $cycle) {
                $cycle->filterTargetCycle = $report->getTargetCycle();
            }

            $filtered = $collection->filter(function ($cycle) {
                $target_end_date = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->filterTargetCycle->end_date . ' 23:59');
                $enddate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date . ' 23:59');

                return $enddate->lte($target_end_date) && ($target_end_date->year == $enddate->year);
            })
                ->sortBy('end_date');

        } else {
            $filtered = $collection->filter(function ($cycle) {

                $now = \Carbon\Carbon::createFromTimestamp(time());
                $enddate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date . ' 23:59');
                return $enddate->lt($now);
            })
                ->sortBy('end_date');
        }


        $this->setEntity($filtered->reverse());

        $this->setReport($report);
    }


    /**
     * @return null|CycleInterface
     */
    public function current()
    {
        if ($targetCycle = $this->getReport()->getTargetCycle()) {
            $return = $this->getEntity()->filter(function ($cycle) use ($targetCycle) {
                return $cycle->id == $targetCycle->id;
            });

            return $return->first();

        } elseif ($this->getEntity()[0]) {

            return $this->getEntity()[0];
        }
        return null;
    }

    /**
     * @return null|CycleInterface
     */
    public function previous()
    {
        if($targetCycle = $this->getReport()->getTargetCycle())
        {
            $return = $this->getEntity()->filter(function ($cycle) use ($targetCycle) {
                return $cycle->id != $targetCycle->id;
            });
            return $return->first();
        }
        elseif (count($this->getEntity()) > 1) {
            return $this->getEntity()[1];
        }
        return null;
    }


    /**
     * @param CycleCollectionInterface $model
     */
    public function setEntity(CycleCollectionInterface $model)
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