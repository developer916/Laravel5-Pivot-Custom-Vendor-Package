<?php
use \Pivotal\Department\Models\DepartmentInterface;
use \Pivotal\School\Models\SchoolInterface;
class DepartmentsControllerBak extends BaseController {

    /**
     * View a Department
     */
    public function view(DepartmentInterface $department) {

        $data = array();
        $data['header'] = $department->name;
        $data['school'] = $department->school;
        $data['department'] = $department;
        $data['classes'] = $department->classes;
        $data['teachers'] = $department->teachers();
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

//        $limedata = new LimeData();

        // we cannot assign an arbitrary attribute to Eloquent data object and we need to display only the classes in the department
        $cycles = array();

        foreach ($department->classes as $class) {
            foreach ($class->cycles as $cycle) {
                // a cycle can have multiple classes
                $cycles[$cycle->id] = $cycle;
            }
        }

        $data['cycles'] = $cycles;

        return $this->get_view('department-view', $data)
                    ->nest('classes_panel', 'course:includes.classes-panel', $data)
                    ->nest('teachers_panel', 'teachers-panel', $data)
                    ->nest('cycle_panel', 'department::includes.department-cycles-panel', $data);
    }

    /**
     * Create or edit a Department
     */
    public function edit(SchoolInterface $school, DepartmentInterface $department = null) {

        $department = $department ?: new Department;

        $data = array();
        $data['header'] = 'Administration | ' . ($department->id ? 'Edit '.$department->name : 'Add new department');
        $data['school'] = $school;
        $data['department'] = $department;

        return $this->get_view('department-edit', $data);
    }

    /**
     * Save a Department
     */
    public function save(SchoolInterface $school, DepartmentInterface $department = null) {

        // get all the data
        $data = Input::all();

        // compose the list of static rules
        $rules = array(
            'name'  => 'required|unique:departments,name,'.($department ? $department->id : 'NULL').',id,school_id,'.$school->id,
        );

        // instantiate the validator
        $validator = Validator::make($data, $rules);

        // check the validation rules
        if ($validator->fails()) {
            Request::flash(); // preserve all the input data for repopulating the form
            return Redirect::back()->withErrors($validator);
        }

        $message = 'Department successfully ' .( ($department) ? 'updated' : 'created');

        $department = $department ?: new Department;
        $department->fill($data);
        $department->school_id = $school->id;
        $department->save();

        // redirect to view page
        return Redirect::to("/department/view/{$department->id}")->with('message', $message);
    }

    /**
     * Delete a Department
     */
    public function delete(DepartmentInterface $department) {

        $department->delete();

        return Redirect::back()->with('message', 'Department successfully deleted');
    }

    /**
     * Does the current user have permission to access this.
     */
    public static function can_access(DepartmentInterface $department) {

        // check the role based permissions
        switch (Auth::user()->role) {

        	case User::PIVOT_ADMIN:
        	    // can do anything
        	    return true;

        	case User::SCHOOL_ADMIN;
        	// can access anything in their school
        	return (Auth::user()->school_id == $department->school_id);

        	case User::DEPARTMENT_HEAD:
        	    // can access anything in their department
        	    return (Auth::user()->department_id == $department->id);

        	case User::TEACHER:
        	    // can access their own departments
        	    return $department->classes->filter(function($class) {
        	        return (Auth::user()->id == $class->teacher_id);
        	    })->count();
        }

        return false;
    }
}