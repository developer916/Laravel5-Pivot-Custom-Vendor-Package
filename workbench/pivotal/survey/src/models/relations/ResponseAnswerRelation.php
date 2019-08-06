<?php namespace Pivotal\Survey\Models\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Pivotal\Survey\Models\Answer;
use Pivotal\Survey\Models\Collections\AnswerCollection;
use Pivotal\Survey\Models\ResponseInterface;
use Pivotal\Survey\Models\SurveyInterface;

class ResponseAnswerRelation extends Relation
{

    /**
     * Create a new relation instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @return void
     */
    public function __construct(Builder $query, ResponseInterface $parent)
    {
        $parent->setSid($parent->sid);
        $this->query = $query->from($parent->getTable())->where('id','=',$parent->id);

        $this->parent = $parent;
        $this->related = $query->getModel();

        $this->addConstraints();
    }


    public function addEagerConstraints(array $models)
    {
        parent::addEagerConstraints($models);
    }

    public function initRelation(array $models, $relation)
    {
        dd('initRelation');
    }

    public function addConstraints()
    {

    }

    public function match(array $models, Collection $results, $relation)
    {

    }

    public function getResults()
    {
        $result = $this->query->first();
        $answerCollection = new AnswerCollection();

        $i = 1;
        foreach($this->parent->survey->questions as $question)
        {
            $newAnswer = new Answer();
            $newAnswer->question = $question;
            $newAnswer->response = $this->parent;
            $newAnswer->index = $i;
            $newAnswer->value = $result->{$question->sid.'X'.$question->gid.'X'.$question->qid};
            $answerCollection->add($newAnswer);
            $i ++;
        }
        return $answerCollection;
    }

}