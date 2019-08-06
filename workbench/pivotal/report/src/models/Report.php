<?php namespace Pivotal\Report\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Pivotal\Models\ReportInterface;
use Pivotal\Report\Models\Collections\ReportCollection;

class Report extends Eloquent implements ReportInterface
{
    protected $table = 'reports';

    //@todo turn these on
    public $timestamps = false;

    protected $fillable = ['cycle_id', 'department_id','school_id','user_id','data'];

    public function newCollection(array $models = [])
    {
        return new ReportCollection($models);
    }


}