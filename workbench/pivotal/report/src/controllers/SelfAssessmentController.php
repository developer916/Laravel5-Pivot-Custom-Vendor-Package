<?php namespace Pivotal\Report\Controllers;



use Pivotal\Survey\Models\Assessment;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use \Validator;
use \Request;
use \Redirect;

class SelfAssessmentController extends BaseReportController
{

    public function view()
    {
        $user = \Auth::user();

        $latest_cycle = \Auth::user()->cycles()->orderBy('start_date',"DESC")->first();

        $now = new \DateTime('now');
        $cycle_end = \DateTime::createFromFormat('d/m/Y',$latest_cycle->end_date);


        //If cycle has ended then redirect user back
        if($cycle_end < $now) return \Redirect::to("/user/view/".\Auth::user()->id)->with('message','Your latest cycle is already complete.');


        $survey = Assessment::where('cycle_id','=',$latest_cycle->id)->where('teacher_id','=',$user->id)->where('q1','!=','null');

        //If we didnt find a survey create one and send the user to it
        if($survey->first() == null)
        {
            $survey_data = [
                'teacher_id' => $user->id,
                'class_id' => null,
                'cycle_id' => $latest_cycle->id,
            ];

            $survey = $survey->getModel();

            $survey->fill($survey_data);
            $survey->save();

            $view_data = [
                'assessment' => $survey,
                'header' => 'Self Assessment',
            ];

            return \View::make('self_assessment', $view_data);


        }else{

            //If the survey is filled out already then redirect the user back
            return \Redirect::to("/user/view/".\Auth::user()->id);

        }

    }


    public function store()
    {
        $assessment = Assessment::where('id','=',\Request::get('assessment_id'))->where('teacher_id','=',\Auth::user()->id)->firstOrFail();

        $values = \Request::get('value', []);
        $required = ['q1', 'q2', 'q3', 'q4', 'q5', 'q6', 'q7', 'q8', 'q9', 'q10', 'q11', 'q12', 'q13', 'q14', 'q15',
            'q16', 'q17', 'q18', 'q19', 'q20', 'q21', 'q22', 'q23', 'q24', 'q25'];
        $rules = [];
        foreach ($required as $r) {
            $rules[$r] = 'required|digits_between:1,5';
        }

        $validator = Validator::make($values, $rules);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $assessment->fill($values);
        $assessment->save();
        return \Redirect::to("/user/view/".\Auth::user()->id);
    }



    /**
     * @description !important this is the old version of the self assessment that used the limesurvey instance
     * @param null $survey_id
     */
    public function viewOld($survey_id = null)
    {
        $user = \Auth::user();

        if(\Input::has('reset'))
        {
            $user->self_sid = null;
            $user->save();
            dd();
        }


        if($user->role != \User::TEACHER && $user->role != \User::DEPARTMENT_HEAD)
        {
            return \Redirect::to('/')->withErrors('You must be a teacher to view this page');
        }

        if($user->self_sid == null)
        {
            return $this->create();
        }

        $old_survey = \Pivotal\Survey\Models\Survey::where('sid','=',\Auth::user()->self_sid)->first();

        if(!$old_survey) {
            return $this->create();
        }

        if(is_object($old_survey))
        {
            //Most recent cycle
            $cycle = \Auth::user()->cycles()->orderBy('start_date',"DESC")->first();


            $limestart = explode('/', $cycle->start_date);
            $limestart = $limestart[2].'-'.$limestart[1].'-'.$limestart[0];

            //If the old survey is from a previous cycle
            if(date('Y-m-d',strtotime($old_survey->startdate)) <= $limestart)
            {
                return $this->create();
            }

        }

        try {
            //We have a survey but no response yet
            if(count($old_survey->responses) < 1)
            {
                return $this->viewSurvey(\Auth::user()->self_sid);
            }
        } catch(\Exception $e) {
            return $this->create();
        }

        return $this->viewReport($old_survey->sid);


    }

    private function create($survey_id = null)
    {
        $user = \Auth::user();
        //Most recent cycle
        $cycle = \Auth::user()->cycles()->orderBy('start_date',"DESC")->first();
        $limesurvey = new \LimeSurvey();

        //Build a new survey if none exists
        if(is_null($survey_id))
        {
            $template = \Config::get('limesurvey.template');
            $surveyurl = \Config::get('limesurvey.surveyurl');
            $adminurl = \Config::get('limesurvey.adminurl');

            $survey_id = $limesurvey->copy_survey($template, 'Self Assessment for '.$user->name. ' Cycle: '. $cycle->name);
        }

        //Build start and end dates
        $yesterday = new \DateTime();
        $yesterday->sub(new \DateInterval('P1D'));

        $limeend = explode('/', $cycle->end_date);
        $limeend = $limeend[2].'-'.$limeend[1].'-'.$limeend[0].' 23:59';

        //Create the survey and set the start and end dates
        $limesurvey->set_survey_properties($survey_id, ['startdate' => $yesterday->format('Y-m-d'), 'expires' => $limeend]);
        $activate = $limesurvey->activate_survey($survey_id);


        //Save the self_sid to the users table
        $user = \Auth::user();
        $user->self_sid = $survey_id;
        $user->save();

        //Set the teacher_self_sid on cycles_classes
        $classes = \Auth::user()->classes;
        foreach($classes as $class)
        {
            foreach($class->cycles as $class_cycle)
            {
                if($class_cycle->id == $cycle->id)
                {
                    $class_cycle->pivot->teacher_self_sid = $survey_id;
                    $class_cycle->pivot->save();
                }
            }
        }

        return $this->view($survey_id);

    }

    private function viewSurvey($sid)
    {
        $surveyurl = \Config::get('limesurvey.surveyurl');
        return \Redirect::away($surveyurl.$sid);
    }


    private function viewReport($sid)
    {
        $survey = \Pivotal\Survey\Models\Survey::where('sid','=',\Auth::user()->self_sid)->first();
        $survey_response = $survey->responses->first();

        die();
    }


}