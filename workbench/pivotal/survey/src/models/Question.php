<?php namespace Pivotal\Survey\Models;

use \Config;
use Pivotal\Survey\Models\Relations\QuestionAnswerRelation;
use Pivotal\Survey\Models\Relations\QuestionResponseRelation;
use \Pivotal\Survey\Models\Response;
use Illuminate\Database\Eloquent\Model;
use Pivotal\Survey\Models\QuestionInterface;

class Question extends Model implements QuestionInterface
{
    protected $connection = 'limemysql';
    protected $table = 'questions';
    protected $primaryKey = 'qid';

    public function __construct($attributes = array())
    {
        $this->table = Config::get('limesurvey.db_prefix').$this->table;
        parent::__construct($attributes);
    }

    public function survey()
    {
        return $this->HasOne('Pivotal\Survey\Models\Survey','sid','sid');
    }

    public function answers()
    {
        $instance = new Response();
        $instance->setSid($this->sid);
        return new QuestionAnswerRelation($instance->newQuery(),$this);
    }
}