<?php

use Codesleeve\Stapler\ORM\EloquentTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class aClassbak extends Eloquent {

	use EloquentTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'classes';

	public $timestamps = true;

	protected $fillable = ['department_id', 'teacher_id', 'name', 'code', 'year_level', 'num_students'];

	/**
	 * Classes belong to a department
	 */
	public function department() {
	    return $this->belongsTo('Department');
	}

	/**
	 * Classes belong to a teacher
	 */
	public function teacher() {
	    return $this->belongsTo('User', 'teacher_id');
	}

	/**
	 * Classes have many cycles
	 */
	public function cycles() {
	    return $this->belongsToMany('Cycle', 'cycles_classes', 'class_id', 'cycle_id')
	                ->withPivot('limesurvey_id', 'url', 'adminurl')->orderBy('name');
	}
}