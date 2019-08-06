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

class SelfAssessment extends Survey implements SurveyInterface
{
    public function user()
    {
        return $this->hasOne('Pivotal\User\Models\User','self_sid','sid');
    }
}