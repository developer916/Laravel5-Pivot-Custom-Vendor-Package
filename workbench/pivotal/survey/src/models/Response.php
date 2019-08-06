<?php namespace Pivotal\Survey\Models;

use \Config;
use Illuminate\Database\Eloquent\Model;
use Pivotal\Survey\Models\Collections\ResponseCollection;
use Pivotal\Survey\Models\Relations\ResponseAnswerRelation;
use Pivotal\Survey\Models\Relations\ResponseSurveyRelation;

class Response extends Model implements ResponseInterface
{
    protected $connection = 'limemysql';
    protected $table = 'survey';

    public function setSid($sid = null)
    {
        $this->sid = $sid;
        $this->table = Config::get('limesurvey.db_prefix').'survey_'.$sid;
    }

    public function survey()
    {
        $instance = new Survey();
        return new ResponseSurveyRelation($instance->newQuery()->where('sid','=',$this->sid),$this);
    }

    public function answers()
    {
        $instance = $this;
        return new ResponseAnswerRelation($instance->query()->from($this->table),$this);
    }

    public function newCollection(array $models = [])
    {
        return new ResponseCollection($models);
    }

}