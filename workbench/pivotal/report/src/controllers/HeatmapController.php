<?php namespace Pivotal\Report\Controllers;

use Pivotal\Report\Repositories\QuestionBreakdownReportRepository;
use \Redirect;
use \Utils;
use \Illuminate\Auth\UserInterface;
use \Pivotal\Cycle\Models\CycleInterface;
use \Pivotal\Department\Models\DepartmentInterface;
use Illuminate\Support\Facades\View;
class HeatmapController extends BaseReportController {

    public function __construct(QuestionBreakdownReportRepository $report)
    {
        $this->report = $report;
    }

    function teacher_page(UserInterface $user, CycleInterface $cycle ) {

        $this->report
            ->setTeacher($user)
            ->setTargetCycle($cycle);

        $school = $user->school;

        $school_surveys = array();
        $teacher_surveys = array();

        $now = \Carbon\Carbon::createFromTimestamp(time());
        $enddate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date.' 23:59');
        if( $enddate->gt($now)) {
            return Redirect::to("/user/view/$user->id")->with('error', 'Cycle still ongoing');
        }

        foreach($cycle->classes as $class) {
            $school_surveys[] = $class->pivot->limesurvey_id;
            if ($class->teacher->id == $user->id) {
                $teacher_surveys[] = $class->pivot->limesurvey_id;
            }
        }

        $limedata = new \LimeData();

        $responses = 0;
        foreach ($teacher_surveys as $survey_ids) {
            $responses += $limedata->count_survey_responses($survey_ids);
        }

        if ($responses < Utils::$responses_threshold) {
            return Redirect::to("/user/view/$user->id")->with('error', 'Report has not yet met survey response number threshold');
        }

        $data = array();

        $data['page'] = 'heatmap';
        $data['header'] = 'Teacher Heatmap';
        $data['headertext'] = 'Summary of results, '.$cycle->name;
        $data['subheader_bold'] = 'Heat Map';
        $data['subheader'] = ', '.$user->name;
        $data['opening_text'] = 'This report provides a summary of your results: the average scores for each question, and (further below) a summary of your highest and lowest scoring Standards and individual questions.';
        $data['additional_info'] = '
                <p><span style="font-weight:bold;">Heat map:</span> You can see your average score for each Standard (the number in the purple square, and as represented by the purple line on the heat map), as well as the school\'s average score for each of the five Australian Professional Standards (the number in the orange dot, and as represented by the orange dotted line on the heat map).  The "wedges" display your average score for a specific question within each of the five Standards, where 1 = strongly disagree, and 5 = strongly agree.  Hover your mouse over a "wedge" to see the question text as well as your numerical average for that question.</p>
                <p><span style="font-weight:bold;">Summary text:</span> The "Area of strength" text describes your highest scoring Standards and bottom three individual questions.  The "Area for growth" text describes your lowest scoring Standards and bottom three individual questions.</p>
        ';

        $data['heatmap_data'] = array();
        $data['heatmap_data']['stats'] = array_values($limedata->get_survey_question_average($teacher_surveys));

        $data['heatmap_data']['lines'] = array();

        $school_question_average = array_values($limedata->get_survey_question_average($school_surveys));

        $aggr_averages = array();

        // calculate the school average for each standard
        for ($i = 0; $i < 5; $i++) {
            $total = 0;
            $aggr_total = 0;
            for ($j =0; $j < 5; $j++) {
                $total += $school_question_average[$i*5 + $j];
                $aggr_total += $data['heatmap_data']['stats'][$i*5 + $j];
            }
            $school_average = $total/5;
            $data['heatmap_data']['boxtext'][$i] = number_format($aggr_total/5 , 1);
            $aggr_averages[] = $data['heatmap_data']['boxtext'][$i];
            $data['heatmap_data']['lines'][] = array(
                'value' => (number_format($school_average, 1) * 1),
                'description' => 'School average - Standard '.($i+1),
                'position' => ($i*5),
                'color'=>'#F7AC5F',
                'linestyle' => 'Dot',
                'visible' => true,
                'width' => 3
            );
            $data['heatmap_data']['lines'][] = array(
                'value' => ($data['heatmap_data']['boxtext'][$i]) * 1.0,
                'description' => 'Your average - Standard '.($i+1),
                'position' => ($i*5),
                'color'=>'#4F4783',
                'linestyle' => 'Solid',
                'visible' => true,
                'width' => 2
            );
        }

        $data['highest'] = array();
        $data['lowest'] = array();

        asort($aggr_averages);

        $count = 0;
        foreach ($aggr_averages as $key => $average) {
            switch ($count) {
                case 0:
                    $low_standard_index_1 = $key;
                    $low_standard_value_1 = $average;
                    break;
                case 1:
                    $low_standard_index_2 = $key;
                    $low_standard_value_2 = $average;
                    break;
                case 3:
                    $high_standard_index_2 = $key;
                    $high_standard_value_2 = $average;
                    break;
                case 4:
                    $high_standard_index_1 = $key;
                    $high_standard_value_1 = $average;
                    break;
            }

            $count++;
        }

        $data['areas_of'] = array();

        if ($high_standard_value_1 == $high_standard_value_2) {
            $data['areas_of']['strength'] = array('Standard '.($high_standard_index_1+1).' - '.Utils::$standards[$high_standard_index_1], 'Standard '.($high_standard_index_2+1).' - '.Utils::$standards[$high_standard_index_2]);
        } else {
            $data['areas_of']['strength'] = array('Standard '.($high_standard_index_1+1).' - '.Utils::$standards[$high_standard_index_1]);
        }

        if ($low_standard_value_1 == $low_standard_value_2) {
            $data['areas_of']['growth'] = array('Standard '.($low_standard_index_1+1).' - '.Utils::$standards[$low_standard_index_1], 'Standard '.($low_standard_index_2+1).' - '.Utils::$standards[$low_standard_index_2]);
        } else {
            $data['areas_of']['growth'] = array('Standard '.($low_standard_index_1+1).' - '.Utils::$standards[$low_standard_index_1]);
        }

        $copy = $data['heatmap_data']['stats'];
        asort($copy);

        $count = 0;
        foreach ($copy as $key => $average) {
            switch ($count) {
                case 0:
                    $low_question_index_1 = $key;
                    $low_question_value_1 = $average;
                    break;
                case 1:
                    $low_question_index_2 = $key;
                    $low_question_value_2 = $average;
                    break;
                case 2:
                    $low_question_index_3 = $key;
                    $low_question_value_3 = $average;
                    break;
                case 3:
                    $low_question_index_4 = $key;
                    $low_question_value_4 = $average;
                    break;
                case 24:
                    $high_question_index_1 = $key;
                    $high_question_value_1 = $average;
                    break;
                case 23:
                    $high_question_index_2 = $key;
                    $high_question_value_2 = $average;
                    break;
                case 22:
                    $high_question_index_3 = $key;
                    $high_question_value_3 = $average;
                    break;
                case 21:
                    $high_question_index_4 = $key;
                    $high_question_value_4 = $average;
                    break;
            }
            $count++;
        }

        $questions = $limedata->get_survey_questions(reset($school_surveys), \LimeData::QUESTION_INDEX_COUNT);
        $data['heatmap_data']['questions'] = array();
        foreach ($questions as $question) {
            $data['heatmap_data']['questions'][] = $question->question;
        }

        $data['high_individual'] = array();
        if ($high_question_value_1 == $high_question_value_2) {
            $data['high_individual'][] = array('col1' => '=1st', 'col2' => 'Q'.($high_question_index_1 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_1]);
            $data['high_individual'][] = array('col1' => '=1st', 'col2' => 'Q'.($high_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_2]);
            if ($high_question_value_3 == $high_question_value_4) {
                $data['high_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
                $data['high_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($high_question_index_4 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_4]);
            } else {
                $data['high_individual'][] = array('col1' => '3rd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
            }
        } else {
            $data['high_individual'][] = array('col1' => '1st', 'col2' => 'Q'.($high_question_index_1 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_1]);
            if ($high_question_value_2 == $high_question_value_3) {
                $data['high_individual'][] = array('col1' => '=2nd', 'col2' => 'Q'.($high_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_2]);
                $data['high_individual'][] = array('col1' => '=2nd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
            } else {
                $data['high_individual'][] = array('col1' => '2nd', 'col2' => 'Q'.($high_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_2]);
                if ($high_question_value_3 == $high_question_value_4) {
                    $data['high_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
                    $data['high_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($high_question_index_4 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_4]);
                } else {
                    $data['high_individual'][] = array('col1' => '3rd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
                }
            }
        }

        if ($low_question_value_1 == $low_question_value_2) {
            $data['low_individual'][] = array('col1' => '=1st', 'col2' => 'Q'.($low_question_index_1 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_1]);
            $data['low_individual'][] = array('col1' => '=1st', 'col2' => 'Q'.($low_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_2]);
            if ($low_question_value_3 == $low_question_value_4) {
                $data['low_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
                $data['low_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($low_question_index_4 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_4]);
            } else {
                $data['low_individual'][] = array('col1' => '3rd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
            }
        } else {
            $data['low_individual'][] = array('col1' => '1st', 'col2' => 'Q'.($low_question_index_1 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_1]);
            if ($low_question_value_2 == $low_question_value_3) {
                $data['low_individual'][] = array('col1' => '=2nd', 'col2' => 'Q'.($low_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_2]);
                $data['low_individual'][] = array('col1' => '=2nd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
            } else {
                $data['low_individual'][] = array('col1' => '2nd', 'col2' => 'Q'.($low_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_2]);
                if ($low_question_value_3 == $low_question_value_4) {
                    $data['low_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
                    $data['low_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($low_question_index_4 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_4]);
                } else {
                    $data['low_individual'][] = array('col1' => '3rd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
                }
            }
        }


        if($previous = $this->report->getTeacher()->surveys()->previous())
        {
            $comparison = $this->report->getTeacher()->surveys()->current()->responses()->answers()->compare($previous->responses()->answers());
            $averages = $comparison->average();
            foreach($averages->take(5) as $value)
            {
                if($value['value'] > 0)
                {
                    $value['value']= '+'.number_format($value['value'],1);
                    $data['improvement_data'][] = $value;
                }
            }
        }


        foreach ($data['heatmap_data']['stats'] as $key => $stats) {
            $data['heatmap_data']['stats'][$key] = round($stats, 1);
        }

        $data['heatmap_data']['standards'] = Utils::$standards;

        $data['intro'] = array('placeholder1' => 'your', 'placeholder2' => 'your feedback');
        $data['heatmap_data']['legend_name'] = 'Your';

        $this->generate_heatmap($data);

        return $this->get_view('report-heatmap', $data);
    }

    function principal_page(UserInterface $principal, CycleInterface $cycle ) {

        $school = $principal->school;

        $now = \Carbon\Carbon::createFromTimestamp(time());
        $enddate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date.' 23:59');
        if( $enddate->gt($now)) {
            return Redirect::to("/school/view/$school->id")->with('error', 'Cycle still ongoing');
        }

        $school_surveys = array();
        $teachers = array();
        foreach($cycle->classes as $class) {
            $school_surveys[] = $class->pivot->limesurvey_id;
            $teachers[] = $class->teacher->name;
        }

        $limedata = new \LimeData();

        $responses = 0;
        foreach ($school_surveys as $survey_ids) {
            $responses += $limedata->count_survey_responses($survey_ids);
        }
        //@todo turn this back on - AJW
//
        if ($responses < Utils::$responses_threshold || count($teachers) < Utils::$teacher_threshold) {
            return Redirect::to("/school/view/$school->id")->with('error', 'Report has not yet met survey response number threshold');
        }

        $data = array();

        $data['page'] = 'heatmap';
        $data['header'] = 'Principal Heatmap';
        $data['headertext'] = 'Summary of results, '.$cycle->name;
        $data['subheader_bold'] = 'Heat Map';
        $data['subheader'] = ', '.$school->name;
        $data['opening_text'] = 'This report provides a summary of the school\'s results: the average scores for each question, and (further below) a summary of the school\'s highest and lowest scoring Standards and individual questions.';
        $data['additional_info'] = '
                <p><span style="font-weight:bold;">Heat map:</span> You can see the school\'s average score for each of the five Australian Professional Standards (the number in the orange dot, and as represented by the orange dotted line on the heat map).  The "wedges" display the average score for a specific question within each of the five Standards, where 1=strongly disagree, and 5=strongly agree. Hover over a "wedge" to see the question text as well as the school\'s numerical average for that question. More detailed results are shown in additional graphs.</p>
                <p><span style="font-weight:bold;">Summary text:</span>The "Area of strength" text describes the school\'s highest scoring Standards and bottom three individual questions.  The "Area for growth" text describes the school\'s lowest scoring Standards and bottom three individual questions.</p>
        ';

        $data['heatmap_data'] = array();
        $data['heatmap_data']['stats'] = array_values($limedata->get_survey_question_average($school_surveys));

        $data['heatmap_data']['lines'] = array();

        $school_averages = array();

        // calculate the school average for each standard
        for ($i = 0; $i < 5; $i++) {
            $total = 0;
            for ($j =0; $j < 5; $j++) {
                $total += $data['heatmap_data']['stats'][$i*5 + $j];
            }
            $school_average = $total/5;
            $school_averages[] = $school_average;
            $data['heatmap_data']['lines'][] = array(
                    'value' => round($school_average, 1),
                    'description' => 'School average - Standard '.($i+1),
                    'position' => ($i*5),
                    'color'=>'#F7AC5F',
                    'linestyle' => 'Dot',
                    'visible' => true,
                    'width' => 3
            );
        }

        $data['highest'] = array();
        $data['lowest'] = array();

        asort($school_averages);

        $count = 0;
        foreach ($school_averages as $key => $average) {
            switch ($count) {
                case 0:
                    $low_standard_index_1 = $key;
                    $low_standard_value_1 = $average;
                    break;
                case 1:
                    $low_standard_index_2 = $key;
                    $low_standard_value_2 = $average;
                    break;
                case 3:
                    $high_standard_index_2 = $key;
                    $high_standard_value_2 = $average;
                    break;
                case 4:
                    $high_standard_index_1 = $key;
                    $high_standard_value_1 = $average;
                    break;
            }

            $count++;
        }

        $data['areas_of'] = array();

        if ($high_standard_value_1 == $high_standard_value_2) {
            $data['areas_of']['strength'] = array('Standard '.($high_standard_index_1+1).' - '.Utils::$standards[$high_standard_index_1], 'Standard '.($high_standard_index_2+1).' - '.Utils::$standards[$high_standard_index_2]);
        } else {
            $data['areas_of']['strength'] = array('Standard '.($high_standard_index_1+1).' - '.Utils::$standards[$high_standard_index_1]);
        }

        if ($low_standard_value_1 == $low_standard_value_2) {
            $data['areas_of']['growth'] = array('Standard '.($low_standard_index_1+1).' - '.Utils::$standards[$low_standard_index_1], 'Standard '.($low_standard_index_2+1).' - '.Utils::$standards[$low_standard_index_2]);
        } else {
            $data['areas_of']['growth'] = array('Standard '.($low_standard_index_1+1).' - '.Utils::$standards[$low_standard_index_1]);
        }

        $copy = $data['heatmap_data']['stats'];
        asort($copy);

        $count = 0;
        foreach ($copy as $key => $average) {
            switch ($count) {
                case 0:
                    $low_question_index_1 = $key;
                    $low_question_value_1 = $average;
                    break;
                case 1:
                    $low_question_index_2 = $key;
                    $low_question_value_2 = $average;
                    break;
                case 2:
                    $low_question_index_3 = $key;
                    $low_question_value_3 = $average;
                    break;
                case 3:
                    $low_question_index_4 = $key;
                    $low_question_value_4 = $average;
                    break;
                case 24:
                    $high_question_index_1 = $key;
                    $high_question_value_1 = $average;
                    break;
                case 23:
                    $high_question_index_2 = $key;
                    $high_question_value_2 = $average;
                    break;
                case 22:
                    $high_question_index_3 = $key;
                    $high_question_value_3 = $average;
                    break;
                case 21:
                    $high_question_index_4 = $key;
                    $high_question_value_4 = $average;
                    break;
            }
            $count++;
        }

        $questions = $limedata->get_survey_questions(reset($school_surveys), \LimeData::QUESTION_INDEX_COUNT);
        $data['heatmap_data']['questions'] = array();
        foreach ($questions as $question) {
            $data['heatmap_data']['questions'][] = $question->question;
        }

        $data['high_individual'] = array();
        if ($high_question_value_1 == $high_question_value_2) {
            $data['high_individual'][] = array('col1' => '=1st', 'col2' => 'Q'.($high_question_index_1 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_1]);
            $data['high_individual'][] = array('col1' => '=1st', 'col2' => 'Q'.($high_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_2]);
            if ($high_question_value_3 == $high_question_value_4) {
                $data['high_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
                $data['high_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($high_question_index_4 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_4]);
            } else {
                $data['high_individual'][] = array('col1' => '3rd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
            }
        } else {
            $data['high_individual'][] = array('col1' => '1st', 'col2' => 'Q'.($high_question_index_1 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_1]);
            if ($high_question_value_2 == $high_question_value_3) {
                $data['high_individual'][] = array('col1' => '=2nd', 'col2' => 'Q'.($high_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_2]);
                $data['high_individual'][] = array('col1' => '=2nd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
            } else {
                $data['high_individual'][] = array('col1' => '2nd', 'col2' => 'Q'.($high_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_2]);
                if ($high_question_value_3 == $high_question_value_4) {
                    $data['high_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
                    $data['high_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($high_question_index_4 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_4]);
                } else {
                    $data['high_individual'][] = array('col1' => '3rd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
                }
            }
        }

        if ($low_question_value_1 == $low_question_value_2) {
            $data['low_individual'][] = array('col1' => '=1st', 'col2' => 'Q'.($low_question_index_1 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_1]);
            $data['low_individual'][] = array('col1' => '=1st', 'col2' => 'Q'.($low_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_2]);
            if ($low_question_value_3 == $low_question_value_4) {
                $data['low_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
                $data['low_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($low_question_index_4 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_4]);
            } else {
                $data['low_individual'][] = array('col1' => '3rd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
            }
        } else {
            $data['low_individual'][] = array('col1' => '1st', 'col2' => 'Q'.($low_question_index_1 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_1]);
            if ($low_question_value_2 == $low_question_value_3) {
                $data['low_individual'][] = array('col1' => '=2nd', 'col2' => 'Q'.($low_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_2]);
                $data['low_individual'][] = array('col1' => '=2nd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
            } else {
                $data['low_individual'][] = array('col1' => '2nd', 'col2' => 'Q'.($low_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_2]);
                if ($low_question_value_3 == $low_question_value_4) {
                    $data['low_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
                    $data['low_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($low_question_index_4 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_4]);
                } else {
                    $data['low_individual'][] = array('col1' => '3rd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
                }
            }
        }

        foreach ($data['heatmap_data']['stats'] as $key => $stats) {
            $data['heatmap_data']['stats'][$key] = round($stats, 1);
        }

        $data['heatmap_data']['standards'] = Utils::$standards;

        $data['intro'] = array('placeholder1' => 'school', 'placeholder2' => 'the whole school');

        $this->generate_heatmap($data);

        return $this->get_view('report-heatmap', $data);
    }

    function department_head_page(DepartmentInterface $department, CycleInterface $cycle ) {

        $limedata = new \LimeData();

        $now = \Carbon\Carbon::createFromTimestamp(time());
        $enddate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date.' 23:59');
        if( $enddate->gt($now)) {
            return Redirect::to("/department/view/$department->id")->with('error', 'Cycle still ongoing');
        }

        $teacher = \Auth::user();

        $school_surveys = array();
        $department_surveys = array();
        $teachers = array();

        $teacherClasses = [];

        foreach($cycle->classes as $class) {
            $school_surveys[] = $class->pivot->limesurvey_id;
            if ($class->department->id == $department->id) {
                $c = $limedata->count_survey_responses($class->pivot->limesurvey_id);
                if ($c > 0) {
                    $department_surveys[] = $class->pivot->limesurvey_id;
                    $teacherClasses[] = $class->teacher_id;
                    $teachers[$class->teacher->name] = $class->teacher->name;
                }
            }
        }

        $teacherClasses = array_unique($teacherClasses);

        $limedata = new \LimeData();

        $responses = 0;
        foreach ($department_surveys as $survey_ids) {
            $responses += $limedata->count_survey_responses($survey_ids);
        }

        if ($responses < Utils::$responses_threshold || count($teachers) < Utils::$teacher_threshold || count($teacherClasses) < Utils::$teacher_threshold) {
            return Redirect::to("/department/view/$department->id")->with('error', 'Report has not yet met survey response number threshold');
        }

        $data = array();

        $data['page'] = 'heatmap';
        $data['header'] = 'Head of Department Heatmap';
        $data['headertext'] = 'Summary of results, '.$cycle->name;
        $data['subheader_bold'] = 'Heat Map';
        $data['subheader'] = ', '.$department->name;
        $data['opening_text'] = 'This report provides a summary of the Department\'s results: the average scores for each question, and (further below) a summary of the Department\'s highest and lowest scoring Standards and individual questions.';
        $data['additional_info'] = '
                <p><span style="font-weight:bold;">Heat map:</span> You can see your Department\'s average score for each Standard (the number in the purple square, and as represented by the purple line on the heat map), as well as the school\'s average score for each of the five Australian Professional Standards (the number in the orange dot, and as represented by the orange dotted line on the heat map).  The "wedges" display your Department\'s average score for a specific question within each of the five Standards, where 1 = strongly disagree, and 5 = strongly agree.  Hover over a "wedge" to see the question text as well as the Department\'s numerical average for that question. More detailed results are shown in additional graphs.</p>
                <p><span style="font-weight:bold;">Summary text:</span> The "Area of strength" text describes the Department\'s highest scoring Standards and bottom three individual questions.  The "Area for growth" text describes the Department\'s lowest scoring Standards and bottom three individual questions.</p>
        ';

        $data['heatmap_data'] = array();
        $data['heatmap_data']['stats'] = array_values($limedata->get_survey_question_average($department_surveys));

        $data['heatmap_data']['lines'] = array();

        $school_question_average = array_values($limedata->get_survey_question_average($school_surveys));

        $aggr_averages = array();

        // calculate the school average for each standard
        for ($i = 0; $i < 5; $i++) {
            $total = 0;
            $aggr_total = 0;
            for ($j =0; $j < 5; $j++) {
                $total += $school_question_average[$i*5 + $j];
                $aggr_total += $data['heatmap_data']['stats'][$i*5 + $j];
            }
            $school_average = $total/5;
            $data['heatmap_data']['boxtext'][$i] = number_format($aggr_total/5 , 1);
            $aggr_averages[] = $data['heatmap_data']['boxtext'][$i];
            $data['heatmap_data']['lines'][] = array(
                    'value' => (number_format($school_average, 1) * 1),
                    'description' => 'School average - Standard '.($i+1),
                    'position' => ($i*5),
                    'color'=>'#F7AC5F',
                    'linestyle' => 'Dot',
                    'visible' => true,
                    'width' => 4
            );
            $data['heatmap_data']['lines'][] = array(
                    'value' => ($data['heatmap_data']['boxtext'][$i] * 1),
                    'description' => $department->name.' Department average - Standard '.($i+1),
                    'position' => ($i*5),
                    'color'=>'#4F4783',
                    'linestyle' => 'Solid',
                    'visible' => true,
                    'width' => 2
            );
        }

        $data['highest'] = array();
        $data['lowest'] = array();

        asort($aggr_averages);

        $count = 0;
        foreach ($aggr_averages as $key => $average) {
            switch ($count) {
                case 0:
                    $low_standard_index_1 = $key;
                    $low_standard_value_1 = $average;
                    break;
                case 1:
                    $low_standard_index_2 = $key;
                    $low_standard_value_2 = $average;
                    break;
                case 3:
                    $high_standard_index_2 = $key;
                    $high_standard_value_2 = $average;
                    break;
                case 4:
                    $high_standard_index_1 = $key;
                    $high_standard_value_1 = $average;
                    break;
            }

            $count++;
        }

        $data['areas_of'] = array();

        if ($high_standard_value_1 == $high_standard_value_2) {
            $data['areas_of']['strength'] = array('Standard '.($high_standard_index_1+1).' - '.Utils::$standards[$high_standard_index_1], 'Standard '.($high_standard_index_2+1).' - '.Utils::$standards[$high_standard_index_2]);
        } else {
            $data['areas_of']['strength'] = array('Standard '.($high_standard_index_1+1).' - '.Utils::$standards[$high_standard_index_1]);
        }

        if ($low_standard_value_1 == $low_standard_value_2) {
            $data['areas_of']['growth'] = array('Standard '.($low_standard_index_1+1).' - '.Utils::$standards[$low_standard_index_1], 'Standard '.($low_standard_index_2+1).' - '.Utils::$standards[$low_standard_index_2]);
        } else {
            $data['areas_of']['growth'] = array('Standard '.($low_standard_index_1+1).' - '.Utils::$standards[$low_standard_index_1]);
        }

        $copy = $data['heatmap_data']['stats'];
        asort($copy);

        $count = 0;
        foreach ($copy as $key => $average) {
            switch ($count) {
                case 0:
                    $low_question_index_1 = $key;
                    $low_question_value_1 = $average;
                    break;
                case 1:
                    $low_question_index_2 = $key;
                    $low_question_value_2 = $average;
                    break;
                case 2:
                    $low_question_index_3 = $key;
                    $low_question_value_3 = $average;
                    break;
                case 3:
                    $low_question_index_4 = $key;
                    $low_question_value_4 = $average;
                    break;
                case 24:
                    $high_question_index_1 = $key;
                    $high_question_value_1 = $average;
                    break;
                case 23:
                    $high_question_index_2 = $key;
                    $high_question_value_2 = $average;
                    break;
                case 22:
                    $high_question_index_3 = $key;
                    $high_question_value_3 = $average;
                    break;
                case 21:
                    $high_question_index_4 = $key;
                    $high_question_value_4 = $average;
                    break;
            }
            $count++;
        }

        $questions = $limedata->get_survey_questions(reset($school_surveys), \LimeData::QUESTION_INDEX_COUNT);
        $data['heatmap_data']['questions'] = array();
        foreach ($questions as $question) {
            $data['heatmap_data']['questions'][] = $question->question;
        }

        $data['high_individual'] = array();
        if ($high_question_value_1 == $high_question_value_2) {
            $data['high_individual'][] = array('col1' => '=1st', 'col2' => 'Q'.($high_question_index_1 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_1]);
            $data['high_individual'][] = array('col1' => '=1st', 'col2' => 'Q'.($high_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_2]);
            if ($high_question_value_3 == $high_question_value_4) {
                $data['high_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
                $data['high_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($high_question_index_4 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_4]);
            } else {
                $data['high_individual'][] = array('col1' => '3rd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
            }
        } else {
            $data['high_individual'][] = array('col1' => '1st', 'col2' => 'Q'.($high_question_index_1 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_1]);
            if ($high_question_value_2 == $high_question_value_3) {
                $data['high_individual'][] = array('col1' => '=2nd', 'col2' => 'Q'.($high_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_2]);
                $data['high_individual'][] = array('col1' => '=2nd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
            } else {
                $data['high_individual'][] = array('col1' => '2nd', 'col2' => 'Q'.($high_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_2]);
                if ($high_question_value_3 == $high_question_value_4) {
                    $data['high_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
                    $data['high_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($high_question_index_4 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_4]);
                } else {
                    $data['high_individual'][] = array('col1' => '3rd', 'col2' => 'Q'.($high_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$high_question_index_3]);
                }
            }
        }

        if ($low_question_value_1 == $low_question_value_2) {
            $data['low_individual'][] = array('col1' => '=1st', 'col2' => 'Q'.($low_question_index_1 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_1]);
            $data['low_individual'][] = array('col1' => '=1st', 'col2' => 'Q'.($low_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_2]);
            if ($low_question_value_3 == $low_question_value_4) {
                $data['low_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
                $data['low_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($low_question_index_4 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_4]);
            } else {
                $data['low_individual'][] = array('col1' => '3rd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
            }
        } else {
            $data['low_individual'][] = array('col1' => '1st', 'col2' => 'Q'.($low_question_index_1 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_1]);
            if ($low_question_value_2 == $low_question_value_3) {
                $data['low_individual'][] = array('col1' => '=2nd', 'col2' => 'Q'.($low_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_2]);
                $data['low_individual'][] = array('col1' => '=2nd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
            } else {
                $data['low_individual'][] = array('col1' => '2nd', 'col2' => 'Q'.($low_question_index_2 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_2]);
                if ($low_question_value_3 == $low_question_value_4) {
                    $data['low_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
                    $data['low_individual'][] = array('col1' => '=3rd', 'col2' => 'Q'.($low_question_index_4 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_4]);
                } else {
                    $data['low_individual'][] = array('col1' => '3rd', 'col2' => 'Q'.($low_question_index_3 + 1), 'col3' => $data['heatmap_data']['questions'][$low_question_index_3]);
                }
            }
        }

        foreach ($data['heatmap_data']['stats'] as $key => $stats) {
            $data['heatmap_data']['stats'][$key] = round($stats, 1);
        }

        $data['heatmap_data']['standards'] = Utils::$standards;

        $data['intro'] = array('placeholder1' => 'department', 'placeholder2' => 'the '.$department->name.' department');
        $data['heatmap_data']['legend_name'] = $department->name.' department';

        $this->generate_heatmap($data);

        return $this->get_view('report-heatmap', $data);
    }

    public function generate_heatmap ($data) {
        $this->js_include('https://code.highcharts.com/highcharts.js');
        $this->js_include('https://code.highcharts.com/highcharts-more.js');
        $this->js_include('https://code.highcharts.com/modules/exporting.js');

        $json = $data['heatmap_data'];

        $this->js_call('show_heatmap', $json, '/javascript/heatmap_chart.js');
        $this->js_call('dropdown_fix', '', '/javascript/dropdown_fix.js');
    }

}