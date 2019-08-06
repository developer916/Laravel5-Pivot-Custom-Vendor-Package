<?php namespace Pivotal\Report\Repositories;

use \DateTime;
use Closure;
use Countable;
use ArrayAccess;
use ArrayIterator;
use CachingIterator;
use Illuminate\Support\Collection;
use JsonSerializable;
use IteratorAggregate;
use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\ArrayableInterface;

use Pivotal\Cycle\Models\Collections\CycleCollection;
use Pivotal\Cycle\Models\Collections\CycleCollectionInterface;
use Pivotal\Cycle\Models\CycleInterface;
use Pivotal\Survey\Models\Collections\AnswerCollection;
use Pivotal\Survey\Models\Collections\AnswerCollectionInterface;
use Pivotal\Survey\Models\Collections\ResponseCollection;
use Pivotal\Survey\Models\Collections\SurveyCollection;
use Pivotal\Survey\Models\Collections\SurveyCollectionInterface;
use Pivotal\Survey\Models\SurveyInterface;


class AnswerCollectionRepository implements AnswerCollectionRepositoryInterface, ArrayAccess, ArrayableInterface, Countable, IteratorAggregate, JsonableInterface, JsonSerializable
{
    private $entity;
    private $parent;
    private $comparison;
    private $comparisonCollection;

    /**
     * @param CycleCollectionInterface $collection
     */
    public function __construct(AnswerCollectionInterface $collection, ResponseCollectionRepositoryInterface $parent)
    {

        //Build a new collection with repository entities instead of physical ones
        $newCollection = new AnswerCollection();

        //Check if we are dealing with an array of indexes
        if (!is_array($collection->first())) {
            foreach ($collection as $answer) {

                if (is_a($answer, 'Pivotal\Survey\Models\AnswerInterface')) {
                    $newAnswer = new AnswerRepository($answer, $this);
                } else {
                    $newAnswer = $answer;
                }

                $newCollection->add($newAnswer);
            }
        } else {

            foreach ($collection as $index => $answerCollection) {
                $newAnswerCollection = new AnswerCollection();

                foreach ($answerCollection as $answer) {

                    $newAnswer = new AnswerRepository($answer, $this);
                    $newAnswerCollection->add($newAnswer);
                }
                $newAnswerCollectionRepository = new AnswerCollectionRepository($newAnswerCollection, $parent);
                $collection->put($index, $newAnswerCollectionRepository);
            }

            $newCollection = $collection;
        }


        $this->setEntity($newCollection);
        $this->setParent($parent);
    }


    /**
     * Set the comparison collection so specific comparisons can be ran
     * @param AnswerCollectionRepository $comparisonCollection
     * @return $this
     */
    public function compare(AnswerCollectionRepository $comparisonCollection)
    {
        $return = new AnswerCollectionRepository($this->getEntity(),$this->getParent());
        $return->setComparisonCollection($comparisonCollection);
        return $return;
    }

    /**
     * Set the comparison collection
     * @param AnswerCollectionRepository $comparisonCollection
     * @return $this
     */
    public function setComparisonCollection(AnswerCollectionRepository $comparisonCollection)
    {
        $this->comparisonCollection = $comparisonCollection;
        return $this;
    }


    /**
     * @return AnswerCollectionRepository
     */
    public function getComparisonCollection()
    {
        return $this->comparisonCollection;
    }


    public function average()
    {
        //If we are not dealing with a comparison of the averages
        if (!isset($this->comparisonCollection)) {
            //If we are dealing with a single answer index instead of all of the answers
            if (!is_array($this->getEntity()->first())) {
                $value_sum = 0;
                foreach ($this->getEntity() as $answer) {
                    if ($answer->value) {
                        $numeric_value = preg_replace('/[a-zA-Z]/', '', $answer->value);
                        $value_sum += $numeric_value;
                    }
                }
                if ($value_sum <= 0) {
                    return 0;
                }
                return $value_sum / $this->count();
            }else {
                //If we are dealing with multiple answer indexes
                //@todo figure this out

            }
        } else {
            $this->comparison['average'] = new Collection();
            //we are dealing with a comparison of the averages

            //If we are dealing with a single answer index
            if (!is_array($this->getEntity()->first()) && !is_object($this->getEntity()->first())) {
                //@todo figure this out
            } else {
                //If we are dealing with a collection of answer index => answer collections
                foreach ($this as $question_index => $answer_collection) {

                    $data = array(
                        'value' => $answer_collection->average() - $this->getComparisonCollection()[$question_index + 1]->average(),
                        'index' => $question_index + 1,
                    );

                    $this->comparison['average']->push($data);
                }
            }


            $this->comparison['average']->sort(function($a, $b){
                if ($a == $b) {
                    return 0;
                }
                return ($a['value'] > $b['value']) ? -1 : 1;
            });


            return $this->comparison['average'];
        }
    }


    /**
     * @todo refactor this out, it should be just byValue() then foreach build a keyed value array
     */
    public function byValueCount()
    {

        $return = array(
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0
        );
        foreach ($this->getEntity() as $answer) {
            $numeric_value = null;
            if ($answer->value) {
                $numeric_value = preg_replace('/[a-zA-Z]/', '', $answer->value);
            }
            if (isset($numeric_value)) {
                if (isset($return[$numeric_value])) {
                    $return[$numeric_value] += 1;
                } else {
                    $return[$numeric_value] = 1;
                }
            }
        }
        return $return;
    }

    /**
     * @param AnswerCollectionInterface $collection
     */
    public function setEntity(AnswerCollectionInterface $collection)
    {
        $this->entity = $collection;
    }

    /**
     * @return AnswerCollectionInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param ResponseCollectionRepositoryInterface $report
     */
    public function setParent(ResponseCollectionRepositoryInterface $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return ResponseCollectionRepositoryInterface
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
        $count = 0;
        foreach ($this->getEntity() as $item) {
            if ($item->value && !is_null($item->value)) {
                $count += 1;
            }
        }
        return $count;
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