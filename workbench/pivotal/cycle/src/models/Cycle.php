<?php namespace Pivotal\Cycle\Models;

use Pivotal\Cycle\Models\Collections\CycleCollection;
use Codesleeve\Stapler\ORM\EloquentTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Cycle extends Eloquent implements CycleInterface
{

    use EloquentTrait;

    protected $table = 'cycles';

    public $timestamps = true;

    protected $fillable = ['name', 'start_date', 'end_date'];

    /**
     * Date Mutators
     */
    public function getDates()
    {
        return ['start_date', 'end_date'] + parent::getDates();
    }

    /**
     * Fetch mutator for start_date property
     *
     * @return void
     */
    public function getStartDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d/m/Y');
    }

    /**
     * Set mutator for start_date property
     *
     * @return void
     */
    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value);
    }

    /**
     * Fetch mutator for end_date property
     *
     * @return void
     */
    public function getEndDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d/m/Y');
    }

    /**
     * Set mutator for end_date property
     *
     * @return void
     */
    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value);
    }

    /**
     * Cycle belong to a School
     */
    public function school()
    {
        return $this->belongsTo('School');
    }

    /**
     * Cycles have many classes
     */
    public function classes()
    {
        return $this->belongsToMany('Pivotal\Course\Models\Course', 'cycles_classes', 'cycle_id', 'class_id')->withTimestamps()
            ->withPivot('limesurvey_id', 'url', 'adminurl')->orderBy('year_level')->orderBy('name');
    }


    public function newCollection(array $models = [])
    {
        return new CycleCollection($models);
    }


}