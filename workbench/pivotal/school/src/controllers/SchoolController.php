<?php namespace Pivotal\School\Controllers;
use Carbon\Carbon;
use \Input;
use \Validator;
use \Redirect;
use \Pivotal\School\Models\School;
use \Pivotal\School\Models\SchoolInterface;

class SchoolController extends BaseSchoolController {

    /**
     * List all the Schools
     */
    public function index() {

        $sortBy = Input::get('sort_by', false);
        $schools = School::all();

        if ($sortBy) {
            $sortAsc = (bool) Input::get('sort_asc', true);
            switch ($sortBy) {
                case 'created':
                    $schools = $schools->sortBy(function($school) {
                        if (count($school->lastCycle) == 0) {
                            return 0;
                        }
                        return $school->lastCycle->get(0)->created_at;
                    });
                    break;
                case 'start':
                    $schools = $schools->sortBy(function($school) {
                        if (count($school->lastCycle) == 0) {
                            return 0;
                        }
                        return Carbon::createFromFormat('d/m/Y', $school->lastCycle->get(0)->start_date)->format('Y-m-d');
                    });
                    break;
                case 'end':
                    $schools = $schools->sortBy(function($school) {
                        if (count($school->lastCycle) == 0) {
                            return 0;
                        }
                        return Carbon::createFromFormat('d/m/Y', $school->lastCycle->get(0)->end_date)->format('Y-m-d');
                    });
                    break;
                case 'name':
                    $schools = $schools->sortBy(function($school) {
                        return $school->name;
                    });
                    break;
            }

            if (!$sortAsc) {
                $schools = $schools->reverse();
            }

        }


        $data = array();
        $data['header'] = 'Schools';
        $data['schools'] = $schools;

        return $this->get_view('school::pages.school-index', $data);
    }

    /**
     * View a School
     */
    public function view(SchoolInterface $school) {
        $data = array();
        $data['header'] = 'School Dashboard | '.$school->name;
        $data['school'] = $school;
        $data['cycles'] = $school->cycles;
        $data['campuses'] = $school->campuses;
        $data['departments'] = $school->departments;
        $data['classes'] = $school->classesForYear();
        $data['teachers'] = $school->teachers;
        $data['deptviewmodal'] = '
                <ul>
                    <li>Click on a Department\'s name to move to the Department page to reveal all classes and teachers within that Department, as well as the name of the Department Head.</li>
                    <li>Click on the [+] button to add a new Department to your school.</li>
                    <li>Click on the [edit icon] button to edit a Department\'s details (its name).</li>
                    <li>Click on the [x] button to delete the Department entirely.</li>
                </ul>
        ';
        $data['classviewmodal'] = '
                <ul>
                    <li>Click on a Class name to reveal the Teacher, Department, Class Code, year level and number of students.  You will also see the survey cycles assigned to the Class, as well as the link to the survey, the number and percentage of respondents.</li>
                    <li>Click on the [+] button to add a new Class to your school.  Note that you will need to have created the Class\' teacher and Department before you create a new class.</li>
                    <li>Click on the [edit icon] button to edit a Class\'s details (including its Teacher, Class name, Class Code, year level and number of students).</li>
                    <li>Click on the [x] button to delete the Class entirely.</li>
                </ul>
        ';
        $data['usersviewmodal'] = '
                <ul>
                    <li>Click on the user\'s name to reveal their role (as Teacher, Department Head or School Admin), email address, Departments and Classes taught.</li>
                    <li>Click on the [+] button to add a new user to your school.</li>
                    <li>Click on the [Edit icon] button to edit the user\'s details (including their role as Teacher, Department Head or School Admin, name, email address and Pivot password).</li>
                    <li>Click on the [x] button to delete the user entirely.</li>
                </ul>
        ';
        $data['cycleviewmodal'] = '
                <ul>
                    <li>Click on the cycle\'s name to reveal details including start and end dates for the survey, the individual classes to be surveyed, weblinks to each class\'s survey, and number of respondents.</li>
                    <li>Click on the [+] button to create a new Survey cycle for your school. Name your survey and select its start and end dates.  Note that you will need to have created Departments, Classes and Teachers in the system before creating a new cycle.</li>
                    <li>Click on the [edit icon] button to edit the Survey cycle details (including the name of the cycle, its start or end dates, and which classes are surveyed).</li>
                    <li>Click on the [x] button to delete the Survey cycle entirely.</li>
                </ul>
        ';
/*
        $cycles = $data['cycles'];
        $data['cycles'] = $cycles->sortBy(function($cycle) {
            return Carbon::createFromFormat('d/m/Y', $cycle->start_date)->format('Y-m-d');
        })->reverse();
*/
        return $this->get_view('school::pages.school-view', $data)
                    ->nest('cycles_panel', 'cycle::includes.cycles-panel', $data)
                    ->nest('departments_panel', 'department::includes.departments-panel', $data)
                    ->nest('classes_panel', 'course::includes.classes-panel', $data)
                    ->nest('teachers_panel', 'teachers-panel', $data);
    }

    /**
     * Create or edit a School
     */
    public function edit(SchoolInterface $school = null) {

        $school = $school ?: new School;

        $data = array();
        $data['header'] = $school ? 'Edit '.$school->name : 'Add new school';
        $data['school'] = $school;
        $data['schooleditmodal'] = '
                <ul>
                    <li>Your school details - full name, abbreviation (2-3 letters) and logo - will be uploaded by Pivot admin.</li>
                    <li>Please contact Pivot admin should you wish to change any of these details, as it will lead to global changes across the system.</li>
                </ul>
        ';

        return $this->get_view('school::pages.school-edit', $data);
    }

    /**
     * Save a School
     */
    public function save(SchoolInterface $school = null) {

        // get all the data
        $data = Input::all();

        // compose the list of static rules
        $rules = array(
            'logo'  => 'mimes:jpeg,gif,png',
            'name'  => 'required',
            'is_campused'  => 'required|boolean',
            'abbr'  => 'required|unique:schools,abbr'.($school ? ','.$school->id : ''),
        );

        // instantiate the validator
        $validator = Validator::make($data, $rules);

        // check the validation rules
        if ($validator->fails()) {
            Request::flash(); // preserve all the input data for repopulating the form
            return Redirect::back()->withErrors($validator);
        }

        $message = 'School successfully ' .( ($school) ? 'updated' : 'created');

        $school = $school ?: new School;
        $school->fill($data);
        $school->save();

        // redirect to view page
        return Redirect::to("/school/view/{$school->id}")->with('message', $message);
    }

    /**
     * Delete a School
     */
    public function delete(SchoolInterface $school) {

        $school->delete();

        return Redirect::back()->with('message', 'School successfully deleted');
    }
}