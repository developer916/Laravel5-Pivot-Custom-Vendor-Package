<?php namespace Pivotal\Survey\Models;

use Illuminate\Database\Eloquent\Model;
class Answer extends Model implements AnswerInterface
{
    protected $fillable = ['id','value'];



}