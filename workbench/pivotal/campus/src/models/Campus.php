<?php namespace Pivotal\Campus\Models;

use Pivotal\Campus\Models\Collections\CampusCollection;
use Pivotal\Survey\Models\Survey;
use Codesleeve\Stapler\ORM\EloquentTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Pivotal\Campus\Models\CampusInterface;
use Pivotal\School\Models\School;

class Campus extends Eloquent implements CampusInterface
{
    use EloquentTrait;

    /**
     * @var string
     */
    protected $table = 'campuses';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $fillable = ['school_id', 'code'];

    /**
     * Classes belong to a School
     */
    public function school() {
        return $this->belongsTo('School');
    }

    /**
     * Campus has many cources
     */
    public function courses() {
        return $this->hasMany('Course');
    }

    public function newCollection(array $models = [])
    {
        return new CampusCollection($models);
    }


}