<?php
use Codesleeve\Stapler\ORM\StaplerableInterface;
use Codesleeve\Stapler\ORM\EloquentTrait;

class Schoolbak extends Eloquent implements StaplerableInterface {

    use EloquentTrait;

    protected $table = 'schools';

    protected $guarded = array('id');

    public $timestamps = true;

    protected $fillable = ['name', 'abbr', 'logo'];

    public function __construct(array $attributes = array()) {

        $this->hasAttachedFile('logo', [ 'styles' =>
            // constrain the dimensions of the logo
            [ 'medium' => 'x100', 'thumb'  => 'x75']
        ]);

        parent::__construct($attributes);
    }

    /**
     * Schools have many departments
     */
    public function departments() {
        return $this->hasMany('Department')->orderBy('name');
    }

    /**
     * Schools have many classes, which belong to the school's departments
     */
    public function classes() {
        return $this->hasManyThrough('aClass', 'Department')->orderBy('name');
    }

    /**
     * Schools have many teachers
     */
    public function teachers() {
        return $this->hasMany('User')->orderBy('name');
    }

    /**
     * Schools have many cycles
     */
    public function cycles() {
        return $this->hasMany('Cycle')->orderBy('name');
    }
}