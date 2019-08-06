<?php namespace Pivotal\Survey\Models\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Pivotal\Survey\Models\Answer;
use Pivotal\Survey\Models\Collections\AnswerCollection;
use Pivotal\Survey\Models\QuestionInterface;
use Pivotal\Survey\Models\SurveyInterface;

class QuestionAnswerRelation extends Relation
{

    /**
     * Create a new relation instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @return void
     */
    public function __construct(Builder $query, QuestionInterface $parent)
    {
        $table = $query->getModel()->getTable();
        $this->query = $query
            ->select(array(
                \DB::raw($parent->sid.'X'.$parent->gid.'X'.$parent->qid . ' AS value'),
                'id'
            ));

        $this->query = $query;
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

    }

    public function addConstraints()
    {

    }

    public function match(array $models, Collection $results, $relation)
    {

    }

    public function getResults()
    {
        $results = $this->query->remember(5)->get();
        $answerCollection = new AnswerCollection();

        foreach($results as $result)
        {
            $answer = new Answer($result->toArray());
            $answer->question = $this->parent;
            $answerCollection->add($answer);
        }

        return $answerCollection;
    }

}