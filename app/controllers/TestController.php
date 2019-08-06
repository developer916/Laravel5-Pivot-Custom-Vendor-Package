<?php


use \Pivotal\Csv\Facades\Csv;
use \Pivotal\Csv\Models\SchoolImport;
use \Pivotal\Course\Models\Course;
use \Pivotal\User\Models\User;

class TestController extends \Pivotal\Cycle\Controllers\CycleController
{
    public function index()
    {

        //1695 Andrew Blair to 1697 & 1696
        //1678  to 1697 & 1696
        //


        $user = \User::where('id','=','1176')->first();


        $proxy_1 = \User::where('id','=','1079')->first()->id;



        $p = $user->proxies()->attach([$proxy_1]);

//        $user = \User::where('id','=','1678')->first();
//
//        $proxy_1 = \User::where('id','=','1697')->first()->id;
//        $proxy_2 = \User::where('id','=','1696')->first()->id;
//        $p = $user->proxies()->attach([$proxy_1,$proxy_2]);       
        
        

    }

    public function resendTeachersWelcomeMails()
    {

        ini_set('memory_limit','-1');
        ini_set('max_execution_time','-1');

        $schoolId = 16;

        $schoolTeachers = User::where('school_id', $schoolId)->where('created_at', '>', '2016-03-01 00:00:00')
            ->orderBy('created_at', 'desc')->get();
        $i = 0;
        foreach ($schoolTeachers as $schoolTeacher) {
            User::create_user_with_new_password_email($schoolTeacher);
            $i++;
        }

        return ['result' => $i . ' mails sent'];


    }

    public function testMailgun()
    {
        $user = User::find(303);
        $password = 'fake_password';

        for ($i = 0; $i <= 100; $i++) {
            \Mail::send('emails.auth.password', array('user' => $user, 'password' => $password), function ($message) use ($user) {
                $message->to('web-Qn8AMw@mail-tester.com', $user->name)->subject('Welcome!');
            });

            \Mail::send('emails.auth.password', array('user' => $user, 'password' => $password), function ($message) use ($user) {
                $message->to('info@ifrond.com', $user->name)->subject('Welcome!');
            });
        }

        return ['result' => 'sent to '.$user->email];
    }








    public function proxies()
    {




    }




    public function indexss()
    {
        $user = \Auth::user();

        echo "<pre>";
        $survey = \Pivotal\Survey\Models\Survey::getModel()->where('sid','=',$user->self_sid)->get();
        foreach($survey as $s)
        {
            var_dump($survey->toArray());
        }

        $user->self_sid = null;
        $user->save();

        dd();



//        dd($survey->toArray());



//        918344

//        $this->clearReportsCache();
    }


    public function clearReportsCache()
    {
        $reports = \Pivotal\Report\Models\Report::truncate();

        var_dump(count($reports));

        echo "report cache cleared";

    }




    public function indexsss()
    {

//        $teacher_survey_ids = Auth::user()->surveys->lists('sid');


//        $teacher = Teacher::where('id','=',4)->first();
//        foreach($teacher->surveys as $class)
//        {
//            var_dump($class);
//        }
//        dd();
//        var_dump($teacher->department->toArray());

//        $survey = \Pivotal\Survey\Models\Survey::where('sid','=','149735')->first();
//
//        foreach($survey->response as $response)
//        {
//            var_dump($response->survey->toArray());
//            var_dump($response->toArray());
//        }
//
//        die();
//        foreach($survey->questions as $question)
//        {
//            var_dump($question->response['id']);
//
//
//
//        }

//        var_dump($survey->toArray());
        die();


        $user = Auth::user();
        $cycle = \Pivotal\Cycle\Models\Cycle::where('id','=','20')->first();


        $school = $user->school;
        $school_surveys = array();
        $teacher_surveys = array();

        $now = \Carbon\Carbon::createFromTimestamp(time());
        $enddate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date.' 23:59');

    }


    public function csvImportTest()
    {
        $row_model = SchoolImport::getModel();
        $school = School::getModel()->where('id','=','1')->first();
        $csv = CSV::load(app_path()."/storage/imports/test.csv", $row_model->setSchool($school));

        $valid_rows = 0;
        $row_count = 2;
        $errors = [];

        foreach ($csv->getRows() as $row) {

            try {
                $row->save();
                if ($row->isValid()) {
                    $valid_rows += 1;
                } else {
                    $errors[$row_count] = $row->getErrors();


                }

            } catch (Exception $e) {
                \Debug::Log($e->getMessage());
            }

            $row_count++;
        }


        //If we have errors build a message bag within a validator
        if(count($errors) > 0)
        {
            //Create a validator object to encapsulate the errors
            $validator = \Validator::make([],[]);
            foreach($errors as $row_number => $row_errors)
            {
                $validator_errors[$row_number] = "#". $row_number . " ";
                foreach($row_errors as $column_name => $column_errors)
                {
                    $validator_errors[$row_number] .= "\"col:".$column_name."\"";
                    foreach($column_errors as $column_error)
                    {
                        $validator_errors[$row_number] .= " ".$column_error;
                    }
                }
                //Add error message to the validator
                $validator->messages()->add($row_number,$validator_errors[$row_number]);
            }
        }



    }

}