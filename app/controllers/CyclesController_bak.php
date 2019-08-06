<?php
use \Pivotal\Cycle\Models\CycleInterface;
use \Pivotal\Department\Models\DepartmentInterface;
use \Pivotal\School\Models\SchoolInterface;
use Carbon\Carbon;
class CyclesControllerBak extends BaseController {

    /**
     * View a Cycle
     */
    public function view(CycleInterface $cycle, DepartmentInterface $department = null) {

        if (Auth::user()->role == User::DEPARTMENT_HEAD && empty($department)) {
            return Redirect::to("/cycle/departmentview/$cycle->id/".Auth::user()->department_id);
        }

        $data = array();
        $data['header'] = $cycle->name;
        if (empty($department)) {
            $data['cycle'] = $cycle;
        } else {
            foreach ($cycle->classes as $key => $class) {
                if ($class->department->id != $department->id) {
                    $cycle->classes->forget($key);
                }
            }
            $data['cycle'] = $cycle;
        }

        $data['cycle'] = $cycle;

        // make a connection to the LimeSurvey DB
        $limedata = new LimeData();

        // fetch the count of responses
        foreach ($cycle->classes as $class) {
            $sid = $class->pivot->limesurvey_id;
            $class->pivot->responses = $limedata->count_survey_responses($sid);
        }

        $cycle->classes = $cycle->classes->sortBy(function($class) {
            return $class->teacher->name;
        });

        return $this->get_view('cycle-view', $data)
                    ->nest('cyclesclasses_panel', 'cycle::includes.cycles-classes-panel', $data);
    }

    /**
     * Create or edit a Cycle
     */
    public function edit(SchoolInterface $school, CycleInterface $cycle = null) {

        $cycle = $cycle ?: new Cycle;

        $data = array();
        $data['header'] = 'Administration | ' . ($cycle->id ? 'Edit '.$cycle->name : 'Add new cycle');
        $data['school'] = $school;
        $data['cycle'] = $cycle;

        return $this->get_view('cycle-edit', $data);
    }

    /**
     * Save a Cycle
     */
    public function save(SchoolInterface $school, CycleInterface $cycle = null) {

        // get all the data
        $data = Input::all();

        // compose the list of static rules
        $rules = array(
            'name'       => 'required|unique:cycles,name,'.($cycle ? $cycle->id : 'NULL').',id,school_id,'.$school->id,
            'start_date' => 'required|date_format:d/m/Y',
            'end_date'   => 'required|date_format:d/m/Y|after:start_date',
        );

        // override the default lang strings
        $messages = array(
            'end_date.after' => 'The end date must be after the start date.'
        );

        // instantiate the validator
        $validator = Validator::make($data, $rules, $messages);

        // check the validation rules
        if ($validator->fails()) {
            Request::flash(); // preserve all the input data for repopulating the form
            return Redirect::back()->withErrors($validator);
        }

        $message = 'Cycle successfully ' .( ($cycle) ? 'updated' : 'created');

        $cycle = $cycle ?: new Cycle;

        // resend reminders if the date has been pushed forward
        if (strtotime($cycle->start_date)  < strtotime($data['start_date'])
         && strtotime($data['start_date']) > time()) {
            $cycle->reminded = 0;
        }

        $cycle->fill($data);
        $cycle->school_id = $school->id;
        $cycle->save();

        // connect to the LimeSurvey web-service
        $limesurvey = new LimeSurvey();

        $limestart = explode('/', $cycle->start_date);
        $limestart = $limestart[2].'-'.$limestart[1].'-'.$limestart[0];
        $limeend = explode('/', $cycle->end_date);
        $limeend = $limeend[2].'-'.$limeend[1].'-'.$limeend[0].' 23:59';

        // update the survey dates on any attached classes
        foreach ($cycle->classes as $class) {
            // set the start and end dates
            $limesurvey->set_survey_properties($class->pivot->limesurvey_id, ['startdate' => $limestart, 'expires' => $limeend]);
        }

        // redirect to view page
        return Redirect::to("/cycle/view/{$cycle->id}")->with('message', $message);
    }

    /**
     * Delete a Cycle
     */
    public function delete(CycleInterface $cycle) {

        $cycle->delete();

        return Redirect::back()->with('message', 'Cycle successfully deleted');
    }

    /**
     * Create or edit a Cycle
     */
    public function edit_class(CycleInterface $cycle) {

        $data = array();
        $data['header'] = 'Manage classes';
        $data['school'] = $cycle->school;
        $data['cycle'] = $cycle;
        $data['classes'] = array();
        $data['selected'] = array();

        if ($cycle->school->classes->count()) {
            // get all the classes (group by department)
            foreach ($cycle->school->classes as $class) {
                $name = $class->name.' Yr'.$class->year_level.' ('.$class->code.')';
                if ($class->department) {
                    $data['classes'][$class->department->name][$class->id] = $name;
                }
            }

            // get all the selected classes
            $data['selected']['classes'] = [];
            foreach ($cycle->classes as $class) {
                $data['selected']['classes'][] = $class->id;
            }
        }

        return $this->get_view('cycle-class-edit', $data);
    }

    /**
     * Save a Cycle
     */
    public function save_class(CycleInterface $cycle) {

        // get the ID of the template survey
        $template = Config::get('limesurvey.template');

        // get the survey URL base
        $surveyurl = Config::get('limesurvey.surveyurl');

        // get the admin URL base
        $adminurl = Config::get('limesurvey.adminurl');

        // connect to the LimeSurvey web-service
        $limesurvey = new LimeSurvey();

        // get the newly selected classes
        $selected = Input::get('classes');
        $selected = $selected ?: [];

        $current = [];

        // get all the current clasess
        foreach ($cycle->classes as $class) {
            $current[] = $class->id;
        }

        $removed = array_diff($current, $selected);
        $added = array_diff($selected, $current);

        // drop the join record
        if (!empty($removed)) {
            $cycle->classes()->detach($removed);
        }

        foreach ($added as $id) {

            // get the class
            $class = aClass::find($id);

            // clone the survey
            $limesurvey_id = $limesurvey->copy_survey($template, 'Survey for '.$class->name.' Yr'.$class->year_level.' ('.$class->code.')');

            $limestart = explode('/', $cycle->start_date);
            $limestart = $limestart[2].'-'.$limestart[1].'-'.$limestart[0];
            $limeend = explode('/', $cycle->end_date);
            $limeend = $limeend[2].'-'.$limeend[1].'-'.$limeend[0]. ' 23:59';

            // set the start and end dates
            $limesurvey->set_survey_properties($limesurvey_id, ['startdate' => $limestart, 'expires' => $limeend]);

            // activate the survey (makes it available on the set dates)
            $limesurvey->activate_survey($limesurvey_id);

            // get the survey URL
            $url = $surveyurl.$limesurvey_id; // TODO pretty URL?

            // create the join record
            $cycle->classes()->attach([$class->id => ['limesurvey_id'=>$limesurvey_id, 'url'=>$url, 'adminurl'=>$adminurl.$limesurvey_id]]);
        }

        // redirect to view page
        return Redirect::to("/cycle/view/{$cycle->id}")->with('message', 'Classes successfully updated');
    }

    /**
     * Delete a Cycle
     */
    public function delete_class(CycleInterface $cycle, ClassInterface $class) {

        // drop the join record
        $cycle->classes()->detach([$class->id]);

        return Redirect::to("/cycle/view/{$cycle->id}")->with('message', 'Class successfully removed');
    }

    /**
     * Notify teachers of their class survey links, one weekday day before commencement
     */
    public function send_notifications() {

        // handle 2015/16 AU national holidays
        $hol = array(
            '2015-01-01', '2016-01-01', // New Year's Day
            '2015-01-26', '2016-01-26', // Australia Day
            '2015-04-03', '2016-03-25', // Good Friday
            '2015-04-04', '2016-03-26', // Easter Saturday
            '2015-04-06', '2016-03-28', // Easter Monday
            '2015-04-25', '2016-04-25', // Anzac Day
            '2015-06-08', '2016-06-13', // Queen's Birthday
            '2015-12-25', '2016-12-26', // Christmas Day
            '2015-12-28', '2016-12-27', // Boxing Day
        );

        // get the next business day from today
        $next = date('Y-m-d', strtotime('+1 Weekday'));

        while (in_array($next, $hol)) {
            // skip over holidays
        	$next = date('Y-m-d', strtotime($next . ' +1 Weekday'));
        }

        // get all the cycles about to commence
        $cycles = Cycle::whereHas('classes', function($q) use ($next) {
            $q->whereRaw('start_date <= ? AND reminded = 0', array($next));
        })->get();

        $sent = 0;

        foreach ($cycles as $cycle) {
            $teachers = array();

            // group the classes by teacher
            foreach ($cycle->classes as $class) {
                $teachers[$class->teacher_id][] = $class;
            }

            // send the teacher emails
            foreach ($teachers as $classes) {
                // get the teacher
                $teacher = reset($classes)->teacher;

                Mail::send('emails.cycles-reminder', array('teacher' => $teacher, 'classes'=>$classes), function($message) use ($teacher) {
                    $message->to($teacher->email, $teacher->name)
                            ->subject('Pivot Student Survey');
                });

                $sent++;
            }

            $cycle->reminded = 1;
            $cycle->save();
        }

        return "Sent $sent survey reminders";
    }

    /**
     * Does the current user have permission to access this.
     */
    public static function can_access(CycleInterface $cycle) {

        // check the role based permissions
        switch (Auth::user()->role) {

        	case User::PIVOT_ADMIN:
        	    // can do anything
        	    return true;

        	case User::SCHOOL_ADMIN;
        	// can access anything in their school
        	return (Auth::user()->school_id == $cycle->school_id);

        	case User::DEPARTMENT_HEAD:
        	    // can access anything in their department
                foreach ($cycle->classes as $class) {
                    if ($class->department->id == Auth::user()->department_id) {
                        return true;
                    }
                }
                return false;

        	case User::TEACHER:
        	    // can access their own classes
                return false;
        }

        return false;
    }
}