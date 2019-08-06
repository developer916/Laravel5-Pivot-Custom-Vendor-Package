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
use Pivotal\Survey\Models\Answer;
use Pivotal\Survey\Models\Collections\AnswerCollection;
use Pivotal\Survey\Models\Collections\ResponseCollectionInterface;
use Pivotal\Survey\Models\Collections\SurveyCollection;
use Pivotal\Survey\Models\Collections\SurveyCollectionInterface;
use Pivotal\Survey\Models\SurveyInterface;


class ResponseCollectionRepository implements ResponseCollectionRepositoryInterface, ArrayAccess, ArrayableInterface, Countable, IteratorAggregate, JsonableInterface, JsonSerializable
{
    private $entity;
    private $parent;
    private $answers;

    /**
     * @param CycleCollectionInterface $collection
     */
    public function __construct(ResponseCollectionInterface $collection, SurveyCollectionRepositoryInterface $parent)
    {
        $this->setEntity($collection);
        $this->setParent($parent);
    }


    public function answers($answer_index = null)
    {
        //If we want the full collection of answers
        if (is_null($answer_index)) {

            $answers = [];
            $answerCollection = new AnswerCollection();
            foreach($this as $response)
            {
                $i = 1;
                foreach($response->survey->questions as $question)
                {
                    if(!isset($answers[$i]))
                    {
                        $answers[$i] = array();
                    }
                    $new_answer = new Answer();
                    $new_answer->response = $response;
                    $new_answer->value = $response->{$question->sid . 'X' . $question->gid . 'X' . $question->qid};
                    $new_answer->questionIndex = $i;
                    array_push($answers[$i],($new_answer));
                    $i++;
                }
            }

            foreach($answers as $k => $v)
            {
                $answerCollection->put($k,$v);
            }


            $answerCollectionRepository = new AnswerCollectionRepository($answerCollection, $this);
            $this->answers = $answerCollectionRepository;
            return $this->answers;

        } else {
            //If we want a specific index of answers
            if (isset($this->answers[$answer_index])) {
                return $this->answers[$answer_index];
            }
            $new_answers = new AnswerCollection();

            foreach ($this as $response) {
                $question = $response->survey->questions[$answer_index - 1];
                $new_answer = new Answer();
                $new_answer->response = $response;
                $new_answer->value = $response->{$question->sid . 'X' . $question->gid . 'X' . $question->qid};
                $new_answers->add($new_answer);

            }


            $answerCollectionRepository = new AnswerCollectionRepository($new_answers, $this);
            $this->answers[$answer_index] = $answerCollectionRepository;
        }



        return $answerCollectionRepository;

    }


    /**
     * @param ResponseCollectionInterface $collection
     */
    public function setEntity(ResponseCollectionInterface $collection)
    {
        $this->entity = $collection;
    }

    /**
     * @return ResponseCollectionInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param ReportRepositoryInterface $report
     */
    public function setParent(SurveyCollectionRepositoryInterface $parent)
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