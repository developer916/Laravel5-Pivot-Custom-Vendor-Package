<?php namespace Pivotal\Survey\Models;

use \Config;
use Illuminate\Database\Eloquent\Model;
use Pivotal\Course\Models\Course;
use Pivotal\Cycle\Models\Cycle;
use Pivotal\Survey\Models\Collections\SurveyCollection;
use Pivotal\Survey\Models\Relations\SurveyCourseRelation;
use Pivotal\Survey\Models\Relations\SurveyCycleRelation;
use Pivotal\Survey\Models\Relations\SurveyResponseRelation;
use Pivotal\Survey\Models\SurveyInterface;

class Assessment extends Model
{

    const STATUS_UNAVAILABLE = 'unavailable';
    const STATUS_INCOMPLETE = 'incomplete';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'teacher_id',
        'class_id',
        'cycle_id',
        'q1',
        'q2',
        'q3',
        'q4',
        'q5',
        'q6',
        'q7',
        'q8',
        'q9',
        'q10',
        'q11',
        'q12',
        'q13',
        'q14',
        'q15',
        'q16',
        'q17',
        'q18',
        'q19',
        'q20',
        'q21',
        'q22',
        'q23',
        'q24',
        'q25',
    ];
    protected $table = 'assessments';


    public function getQuestionValue($id = null)
    {
        return $this->{'q'.$id};
    }


    public function user()
    {
        return $this->hasOne('Pivotal\User\Models\User','self_sid','sid');
    }
}