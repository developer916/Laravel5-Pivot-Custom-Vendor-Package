<?php namespace Pivotal\Course\Controllers;

use \Input;
use \Validator;
use \Redirect;
use \Request;
use Pivotal\Course\Models\Course;
use \Pivotal\Models\CourseInterface;
use \Pivotal\School\Models\SchoolInterface;
use \Pivotal\Department\Models\DepartmentInterface;

class CourseController extends BaseCourseController {

    /**
     * View a Course
     */
    public function view(CourseInterface $class) {

        $data = array();
        $data['header'] = $class->name.' Yr'.$class->year_level.' ('.$class->code.')';
        $data['class'] = $class;
        $data['department'] = $class->department;
        $data['school'] = $class->department->school;

        // make a connection to the LimeSurvey DB
        $limedata = new \LimeData();

        // fetch the count of responses
        foreach ($class->cycles as $cycle) {
            $sid = $cycle->pivot->limesurvey_id;
            $cycle->pivot->responses = $limedata->count_survey_responses($sid);
        }

        return $this->get_view('course::pages.class-view', $data)
                    ->nest('classescycles_panel', 'cycle::includes.classes-cycles-panel', $data);
    }

    /**
     * Create or edit a Class
     */
    public function edit(SchoolInterface $school, DepartmentInterface $department = null, CourseInterface $class = null) {

        $class = $class ?: Course::getModel();
        $class->department_id = $department ? $department->id : null;

        $yearlevels = array();
        for ($i = 3; $i <= 12; $i++) {
            $yearlevels[$i] = $i;
        }

        $data = array();
        $data['header'] = 'Administration | ' . ($class->id ? 'Edit '.$class->name.' Yr'.$class->year_level.' ('.$class->code.')' : 'Add new class');
        $data['school'] = $school;
        $data['department'] = $department;
        $data['class'] = $class;
        $data['teachers'] = [''=>''] + $school->teachers->lists('name', 'id');
        $data['yearlevels'] = [''=>''] + $yearlevels;
        $data['departments'] = [''=>''] + $school->departments->lists('name', 'id');
        $data['classeditmodal'] = '
                <ul>
                    <li>Select the Department, Class\'s Teacher, and Year level from the drop down lists.</li>
                    <li>Type in a Name for the class and a Class Code, and specify the Number of students.</li>
                    <li>Note that your Class Code should include some identifying numbers or letters denoting when the Class is run (for example, Maths Class 7A running only in Semester 1 might have the Class Code: MAT7AS1).</li>
                </ul>
        ';

        return $this->get_view('course::pages.class-edit', $data);
    }

    /**
     * Save a Class
     */
    public function save(SchoolInterface $school, CourseInterface $class = null) {

        if (!$class) {
            $isNew = true;
        } else {
            $isNew = false;
        }

        // get all the data
        $data = Input::all();

        // compose the list of static rules
        $rules = array(
            'department_id' => 'required|exists:departments,id',
            'teacher_id'    => 'required|exists:users,id',
            //'code'          => 'required|unique:classes,code,'.($class ? $class->id : 0).',id,department_id,'.Input::get('department_id'),
            'code'          => 'required|unique:classes,code,'.($class ? $class->id : 0).',id,department_id,'.Input::get('department_id').',start_year,'.($class ? $class->start_year : date('Y')),
            'name'          => 'required',
            'year_level'    => 'required|integer',
            'num_students'  => 'required|integer',
        );

        // override the default lang strings
        $messages = array(
            'department_id.required' => 'The department field is required.',
            'teacher_id.required'    => 'The class field is required.',
        );

        // instantiate the validator
        $validator = Validator::make($data, $rules, $messages);

        // check the validation rules
        if ($validator->fails()) {
            Request::flash(); // preserve all the input data for repopulating the form
            return Redirect::back()->withErrors($validator);
        }



        $message = 'Class successfully ' .( ($class) ? 'updated' : 'created');

        $class = $class ?: Course::getModel();
        $class->fill($data);
        if ($isNew) {
            $class->start_year = date('Y');
        }
        $class->save();

        // redirect to view page
        return Redirect::to("/class/view/{$class->id}")->with('message', $message);
    }

    /**
     * Delete a Class
     */
    public function delete(CourseInterface $class) {

        $class->delete();

        return Redirect::back()->with('message', 'Class successfully deleted');
    }
}