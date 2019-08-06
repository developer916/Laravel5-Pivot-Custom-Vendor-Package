<?php namespace Pivotal\Cycle\Controllers;
use \Auth;
use \Config;
use \Pivotal\Course\Models\Course;
use Pivotal\Models\CourseInterface;
use \User;
use \Input;
use \Validator;
use \Request;
use \Redirect;
use \Mail;
use \Pivotal\Cycle\Models\Cycle;
use \Pivotal\Cycle\Models\CycleInterface;
use \Pivotal\Department\Models\DepartmentInterface;
use \Pivotal\School\Models\SchoolInterface;
use Carbon\Carbon;
class CycleController extends BaseCycleController {

    /**
     * View a Cycle
     */
    public function view(CycleInterface $cycle, DepartmentInterface $department = null) {

        if (Auth::user()->role == User::DEPARTMENT_HEAD && empty($department)) {
            return Redirect::to("/cycle/departmentview/$cycle->id/".Auth::user()->department_id);
        }

        $user = Auth::user();
        if ($user->role == \User::DEPARTMENT_HEAD && $user->department_id == $department->id) {
            $isDepartmentHeadAtHisDepartmentPage = true;
        } else {
            $isDepartmentHeadAtHisDepartmentPage = false;
        }

        $data = array();
        $data['header'] = $cycle->name;
        if (empty($department)) {
            $data['cycle'] = $cycle;
        } else {
            foreach ($cycle->classes as $key => $class) {
                if ($class->department->id != $department->id && ($isDepartmentHeadAtHisDepartmentPage && $class->teacher_id != $user->id)) {
                    $cycle->classes->forget($key);
                }
            }
            $data['cycle'] = $cycle;
        }

        $data['cycle'] = $cycle;
        $cycleDate =  \DateTime::createFromFormat('d/m/Y', $cycle->start_date);
        $cycleYear = $cycleDate->format('Y');

        $data['cycleYear'] = $cycleYear;

        // make a connection to the LimeSurvey DB
        $limedata = new \LimeData();

        // fetch the count of responses
        foreach ($cycle->classes as $class) {
            $sid = $class->pivot->limesurvey_id;
            try {
                $class->pivot->responses = $limedata->count_survey_responses($sid);
            } catch (\Exception $e) {
                $class->pivot->responses = 'err';
            }

        }

        $cycle->classes = $cycle->classes->sortBy(function($class) {
            return $class->teacher->name;
        });

        return $this->get_view('cycle::pages.cycle-view', $data)
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

        return $this->get_view('cycle::pages.cycle-edit', $data);
    }

    /**
     * Save a Cycle
     */
    public function save(SchoolInterface $school, CycleInterface $cycle = null) {

        ini_set('memory_limit','-1');
        ini_set('max_execution_time','-1');

        // get all the data
        $data = Input::all();

        // compose the list of static rules
        $rules = array(
            'name'       => 'required',
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
        $limesurvey = new \LimeSurvey();

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

        if ($cycle->school->classesForYear()->count()) {
            // get all the classes (group by department)
            foreach ($cycle->school->classesForYear() as $class) {
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

        return $this->get_view('cycle::pages.cycle-class-edit', $data);
    }

    /**
     * Save a Cycle
     */
    public function save_class(CycleInterface $cycle) {

        ini_set('memory_limit','-1');
        ini_set('max_execution_time','-1');

        // get the ID of the template survey
        $template = Config::get('limesurvey.template');

        // get the survey URL base
        $surveyurl = Config::get('limesurvey.surveyurl');

        // get the admin URL base
        $adminurl = Config::get('limesurvey.adminurl');

        // connect to the LimeSurvey web-service
        $limesurvey = new \LimeSurvey();

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
            $class = Course::find($id);

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
    public function delete_class(CycleInterface $cycle, CourseInterface $class) {

        // drop the join record
        $cycle->classes()->detach([$class->id]);

        return Redirect::to("/cycle/view/{$cycle->id}")->with('message', 'Class successfully removed');
    }

    /**
     * Notify teachers of their class survey links, 3 weekday day before the cycle end
     */
    public function send_pre_notifications($test = false,$toemail = null) {

        //ini_set('memory_limit','-1');
        //ini_set('max_execution_time','-1');

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
        $next = date('Y-m-d', strtotime('+3 Weekday'));
        while (in_array($next, $hol)) {
            $next = date('Y-m-d', strtotime($next . ' +1 Weekday'));
        }
        $cycles = Cycle::whereHas('classes', function($q) use ($next) {
            $q->where('end_date', $next);
        })->get();
        $sent = 0;
        $limedata = new \LimeData();
        //dd($cycles->toArray());
        if (count($cycles) > 0) {
            foreach ($cycles as $cycle) {
                $teachers = array();
                try {
                    if ($cycle && $cycle->classes != false && count($cycle->classes) > 0) {
                        foreach ($cycle->classes as $class) {
                            $sid = $class->pivot->limesurvey_id;
                            try {
                                $class->pivot->responses = $limedata->count_survey_responses($sid);
                            } catch (\Exception $e) {
                                $class->pivot->responses = 'err';
                            }
                            $teachers[$class->teacher_id][] = $class;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::debug('CycleController->send_pre_notifications() failed for cycle');
                }
                try {
                    foreach ($teachers as $classes) {
                        $teacher = reset($classes)->teacher;
                        if($test == false) {
                            try {
                                Mail::send('emails.cycles-pre-reminder', array('teacher' => $teacher, 'classes' => $classes, 'cycle' => $cycle), function ($message) use ($teacher) {
                                    $message->to($teacher->email, $teacher->name)
                                        ->subject('Pivot student survey - closing soon at '.$teacher->school->name);
                                });
                            } catch (\Exception $e) {
                                \Log::debug('CycleController->send_pre_notifications() failed');
                            }
                        }
                        $sent++;
                    }
                } catch (\Exception $e) {
                    \Log::debug('CycleController->send_pre_notifications() failed for cycle');
                }
            }
        }

        return "Sent $sent survey reminders ";
    }

    /**
     * Notify teachers of their class survey links once by cycles ids
     */
    public function send_pre_notifications_once($test = false,$toemail = null) {

        ini_set('memory_limit','-1');
        ini_set('max_execution_time','-1');

        $sent = 0;
        $limedata = new \LimeData();
        $cycles = array();
        $cycleIds = array(96, 71, 100, 77);
        foreach ($cycleIds as $id) {
            $cycles[] = Cycle::find($id);
        }
        //dd($cycles);
        foreach ($cycles as $cycle) {
            $teachers = array();
            try {
                if ($cycle && $cycle->classes != false && count($cycle->classes) > 0) {
                    foreach ($cycle->classes as $class) {
                        $sid = $class->pivot->limesurvey_id;
                        try {
                            $class->pivot->responses = $limedata->count_survey_responses($sid);
                        } catch (\Exception $e) {
                            $class->pivot->responses = 'err';
                        }
                        $teachers[$class->teacher_id][] = $class;
                    }
                }
            } catch (\Exception $e) {
                \Log::debug('CycleController->send_pre_notifications() failed for cycle');
            }
            try {
                foreach ($teachers as $classes) {
                    $teacher = reset($classes)->teacher;
                    if($test == false) {
                        try {
                            Mail::send('emails.cycles-pre-reminder', array('teacher' => $teacher, 'classes' => $classes, 'cycle' => $cycle), function ($message) use ($teacher) {
                                $message->to($teacher->email, $teacher->name)
                                    ->subject('Pivot student survey - closing soon at '.$teacher->school->name);
                            });
                        } catch (\Exception $e) {
                            \Log::debug('CycleController->send_pre_notifications() failed');
                        }
                    }
                    $sent++;
                }
            } catch (\Exception $e) {
                \Log::debug('CycleController->send_pre_notifications() failed for teachers');
            }
        }

        return "Sent $sent survey reminders ";
    }

    /**
     * Notify teachers of their class survey links, one weekday day before commencement
     */
    public function send_notifications($test = false,$toemail = null) {

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

                if($test == false)
                {
                    try {
                        Mail::send('emails.cycles-reminder', array('teacher' => $teacher, 'classes' => $classes, 'cycle' => $cycle), function ($message) use ($teacher) {
                            $message->to($teacher->email, $teacher->name)
                                ->subject('Pivot Student Survey: Cycle Reminder');
                        });
                    } catch (\Exception $e)
                    {
                        \Log::debug('CycleController->send_notifications()');

                    }
                } else{
                    Mail::send('emails.cycles-reminder', array('teacher' => $teacher, 'classes'=>$classes, 'cycle' => $cycle), function($message) use ($teacher,$toemail) {
                        echo $teacher->email;
                        echo "<br>";
                        echo $teacher->name;
                        echo "<br>";
                        echo "<br>";

                        $message->to($toemail, $teacher->name)
                            ->subject('Pivot Student Survey: Cycle Reminder Test');
                    });
                }
                $sent++;
            }

            if($test == false) {
                $cycle->reminded = 1;
                $cycle->save();
            }else{
                echo $sent;
            }
        }

        return "Sent $sent survey reminders ";
    }

    /**
     * Generate fake data for class survey
     */
    public function fake_class(CycleInterface $cycle, CourseInterface $class) {

        ini_set('max_execution_time','-1');
        $message = 'Nothing was generated';
        $limesurvey = new \LimeSurvey();
        $limedata = new \LimeData();
        foreach($cycle->classes as $c) {
            if ($class->id == $c->id) {
                if ($c->num_students > $c->pivot->responses) {
                    $answersNum = $c->num_students - $c->pivot->responses;
                    $message = $answersNum. ' new responses was generated';
                    $limesurveyId = $c->pivot->limesurvey_id;
                    $fields = $limedata->get_survey_responses_fields($limesurveyId);
                    $response = [];
                    for ($i = 0; $i < $answersNum; $i++) {
                        foreach ($fields as $f) {
                            $rand = rand(1, 5);
                            $response[$f] = 'A'.$rand;
                        }
                        $d = $limesurvey->add_response($limesurveyId, $response);
                    }
                }
            }
        }

        return Redirect::to("/cycle/view/{$cycle->id}")->with('message', $message);
    }

}