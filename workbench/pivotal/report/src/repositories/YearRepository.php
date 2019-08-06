<?php namespace Pivotal\Report\Repositories;

use Illuminate\Auth\UserInterface;
use Illuminate\Database\Eloquent\Model;
use Pivotal\Cycle\Models\CycleInterface;
use Pivotal\Survey\Models\Collections\SurveyCollection;
use Pivotal\Survey\Models\Collections\SurveyCollectionInterface;
use Pivotal\User\Repositories\UserRepositoryInterface;

class YearRepository extends Model implements YearRepositoryInterface
{

    public $surveys;
    public $year_level;

    public function surveys()
    {
        if (isset($this->surveys)) {
            return $this->surveys;
        }
        return array();
    }

}