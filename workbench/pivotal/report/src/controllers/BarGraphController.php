<?php namespace Pivotal\Report\Controllers;
use \Redirect;
use \Utils;
use \Illuminate\Auth\UserInterface;
use \Pivotal\Cycle\Models\CycleInterface;
use \Pivotal\Department\Models\DepartmentInterface;

class BarGraphController extends BaseReportController {
    public function principal_page(UserInterface $teacher, CycleInterface $cycle, $mode=1) {
        $school = $teacher->school;

        $data = array();
        $chartdata = array();
        $limedata = new \LimeData();

        $now = \Carbon\Carbon::createFromTimestamp(time());

        $enddate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date.' 23:59');

        if( $enddate->gt($now)) {
            return Redirect::to("/school/view/$school->id")->with('error', 'Cycle still ongoing');
        }

        $data['page'] = 'bar_graph';
        $data['subheader_bold'] = 'Bar Graph';
        $data['subheader'] = ', '.$school->name;
        $data['headertext'] = 'Scores for each standard by Department/Year Level';
        $data['header'] = 'Principal\'s Bar Graph for '.$cycle->name;
        $data['cycle'] = $cycle;
        $data['teacher_id'] = $teacher->id;
        $data['display_mode'] = $mode == 2 ? 1 : 2;
        $data['display_mode_text'] = $mode == 2 ? 'Department' : 'Year Level';
        $data['additional_info'] = '
                <p>This report shows each average scores of each Department or Year level, for each Standard. The school average for each Standard is also shown.</p>
                <p>At the top of the page, select the button "Show Department breakdown" or "Show Year Level breakdown" to reveal results either by Department or by Year level, for every Standard.</p>
                <p>The orange dot represents the school average score for that Standard, where 1=strongly disagree and 5=strongly agree.  Hover your mouse over a bar on the graph to see the Department/Year level numerical average score for that Standard.  Hover over the Standard number to reveal the description of that Standard.</p>
                <p>Note that where a Department or year level has fewer than three teachers, the data will not be revealed to protect the anonymity of the teachers.</p>
        ';

        $school_survey_ids = array();
        $aggr_surveys = array();

        $teacherClasses = [];

        foreach ($cycle->classes as $class) {
            $school_survey_ids[] = $class->pivot->limesurvey_id;
            $tResponses = $limedata->count_survey_responses($class->pivot->limesurvey_id);
            if ($tResponses > 0) {
                $teacherClasses[] = $class->teacher_id;
            }
            if ($mode == 1) {
                if ($tResponses > 0) {
                    $data['data_type'] = 'Department';
                    $aggr_surveys[$class->department->name]['teachers'][$class->teacher->name] = 1;
                    $aggr_surveys[$class->department->name]['surveys'][] = $class->pivot->limesurvey_id;
                }
            } else {
                $data['data_type'] = 'Year level';
                $aggr_surveys['Year '.$class->year_level]['teachers'][$class->teacher->name] = 1;
                $aggr_surveys['Year '.$class->year_level]['surveys'][] = $class->pivot->limesurvey_id;
            }
        }

        $teacherClasses = array_unique($teacherClasses);

        foreach ($aggr_surveys as $aggr => $classdata) {
            if (count($classdata['teachers']) < Utils::$teacher_threshold) {
                unset($aggr_surveys[$aggr]['surveys']);
            }
        }

        $responses = 0;

        foreach ($school_survey_ids as $survey_id) {
            $responses += $limedata->count_survey_responses($survey_id);
        }

        if (count($teacherClasses) < Utils::$teacher_threshold) {
            return Redirect::to("/school/view/$school->id")->with('error', 'Report has not yet met survey response number threshold');
        }

        if ($responses < Utils::$responses_threshold) {
            return Redirect::to("/school/view/$school->id")->with('error', 'Report has not yet met survey response number threshold');
        }

        $school_averages = $limedata->get_survey_question_average($school_survey_ids);

        $chartdata['question_blocks'] = array();
        $aggr_averages = array();

        foreach ($aggr_surveys as $aggr => $surveys) {
            if (!empty($surveys['surveys'])) {
                $aggr_averages[$aggr] = $limedata->get_survey_question_average($surveys['surveys']);
            } else {
                $aggr_averages[$aggr] = array();
            }
        }

        $qnumber = 1;

        array_multisort(array_keys($aggr_averages), SORT_NATURAL, $aggr_averages);

        foreach ($aggr_averages as $aggr => $average) {
            for ($i = 0; $i < 5; $i++) {
                $total = 0;
                for ($j = 0; $j < 5; $j++) {
                    $total += $school_averages[($i * 5 + $j)];
                }
                $chartdata['school_averages'][$i] = number_format(($total / 5),1);
            }
            if (empty($average)) {
                $data['question_blocks'][$aggr]['qnumber'] = $qnumber;
                $data['question_blocks'][$aggr]['averages'] = array();
                $qnumber += 1;
                continue;
            }
            $chartdata['question_blocks'][$aggr]['qnumber'] = $qnumber;
            $chartdata['question_blocks'][$aggr]['averages'] = array();
            for ($i = 0; $i < 5; $i++) {
                $aggrtotal = 0;
                for ($j = 0; $j < 5; $j++) {
                    $aggrtotal += $average[($i * 5 + $j)];
                }

                $chartdata['question_blocks'][$aggr]['aggraverages'][$i] = number_format(($aggrtotal / 5),1);
                $data['question_blocks'][$aggr] = $chartdata['question_blocks'][$aggr];
            }
            $qnumber += 1;
        }

        $chartdata['data_type'] = $data['data_type'];
        $data['averages'] = $chartdata['school_averages'];
        $data['standards'] = Utils::$standards;
        $chartdata['standards'] = Utils::$standards;
        $this->draw_bar_graph($chartdata);
        $this->invoke_tooltip();

        $data['report_title'] = $teacher->school->name;
        $data['report_subtitle'] = 'School Summary Bar Graph';
        $data['report_cycletitle'] = $cycle->name . ' ' . $cycle->end_date;



        return $this->get_view('report-bar_graph', $data);

    }

    public function department_head_page(DepartmentInterface $department, CycleInterface $cycle, $mode=1) {

        $now = \Carbon\Carbon::createFromTimestamp(time());
        $enddate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date.' 23:59');
        if( $enddate->gt($now)) {
            return Redirect::to("/department/view/$department->id")->with('error', 'Cycle still ongoing');
        }

        $teacher = \Auth::user();

        $data = array();
        $chartdata = array();
        $limedata = new \LimeData();

        $data['page'] = 'bar_graph';
        $data['subheader_bold'] = 'Bar Graph';
        $data['subheader'] = ', '.$cycle->name;

        $data['headertext'] = 'Scores for each standard by Year Level for '.$department->name;
        $data['header'] = 'Head of Department\'s Bar Graph for '.$cycle->name;
        $data['cycle'] = $cycle;
        $data['additional_info'] = '
                <p>This report shows each average scores for each Year level within your Department, for each Standard. The school\'s average for each Standard is also shown.</p>
                <p>The orange dot represents the school\'s average score for that Standard, where 1 = strongly disagree and 5 = strongly agree.  Hover your mouse over a bar on the graph to see the numerical average score for each Year Level within your Department, for that Standard.  Hover over the Standard number to reveal the description of that Standard.</p>
                <p>Note that where a Year level has fewer than three teachers, the data will not be revealed to protect the anonymity of the teachers (the relevant section will read "N/A").</p>
        ';

        $school_survey_ids = array();
        $aggr_surveys = array();

        $department_surveys = array();

        $data['data_type'] = 'Year level';

        foreach ($cycle->classes as $class) {
            $school_survey_ids[] = $class->pivot->limesurvey_id;

            if ($class->department->id == $department->id) {
                $c = $limedata->count_survey_responses($class->pivot->limesurvey_id);
                if ($c > 0) {
                    $aggr_surveys['Year ' . $class->year_level]['teachers'][$class->teacher->name] = 1;
                    $aggr_surveys['Year ' . $class->year_level]['surveys'][] = $class->pivot->limesurvey_id;
                    $department_surveys[] = $class->pivot->limesurvey_id;
                }
            }
        }

        foreach ($aggr_surveys as $aggr => $classdata) {
            if (count($classdata['teachers']) < Utils::$teacher_threshold) {
                unset($aggr_surveys[$aggr]['surveys']);
            }
        }

        $responses = 0;

        foreach ($department_surveys as $survey_id) {
            $responses += $limedata->count_survey_responses($survey_id);
        }

        if ($responses < Utils::$responses_threshold) {
            return Redirect::to("/department/view/$department->id")->with('error', 'Report has not yet met survey response number threshold');
        }

        $school_averages = $limedata->get_survey_question_average($school_survey_ids);

        $chartdata['question_blocks'] = array();
        $aggr_averages = array();

        foreach ($aggr_surveys as $aggr => $surveys) {
            if (!empty($surveys['surveys'])) {
                $aggr_averages[$aggr] = $limedata->get_survey_question_average($surveys['surveys']);
            } else {
                $aggr_averages[$aggr] = array();
            }
        }

        $qnumber = 1;

        array_multisort(array_keys($aggr_averages), SORT_NATURAL, $aggr_averages);

        foreach ($aggr_averages as $aggr => $average) {
            for ($i = 0; $i < 5; $i++) {
                $total = 0;
                for ($j = 0; $j < 5; $j++) {
                    $total += $school_averages[($i * 5 + $j)];
                }
                $chartdata['school_averages'][$i] = number_format(($total / 5),1);
            }
            if (empty($average)) {
                $data['question_blocks'][$aggr]['qnumber'] = $qnumber;
                $data['question_blocks'][$aggr]['averages'] = array();
                $qnumber += 1;
                continue;
            }
            $chartdata['question_blocks'][$aggr]['qnumber'] = $qnumber;
            $chartdata['question_blocks'][$aggr]['averages'] = array();
            for ($i = 0; $i < 5; $i++) {
                $aggrtotal = 0;
                for ($j = 0; $j < 5; $j++) {
                    $aggrtotal += $average[($i * 5 + $j)];
                }
                $chartdata['question_blocks'][$aggr]['aggraverages'][$i] = number_format(($aggrtotal / 5),1);
                $data['question_blocks'][$aggr] = $chartdata['question_blocks'][$aggr];
            }
            $qnumber += 1;
        }

        $chartdata['data_type'] = $data['data_type'];
        $data['averages'] = $chartdata['school_averages'];
        $chartdata['standards'] = Utils::$standards;
        $data['standards'] = Utils::$standards;
        $this->draw_bar_graph($chartdata);
        $this->invoke_tooltip();

        $data['report_title'] = $department->school->name;
        $data['report_subtitle'] = $department->name . ' Bar Graph';
        $data['report_cycletitle'] = $cycle->name . ' ' . $cycle->end_date;

        return $this->get_view('report-bar_graph', $data);

    }

    public function draw_bar_graph ($data) {
        $this->js_include('https://code.highcharts.com/highcharts.js');
        $this->js_include('https://code.highcharts.com/highcharts-more.js');
        $this->js_include('https://code.highcharts.com/modules/exporting.js');

        $json = $data;

        $this->js_call('draw_bar_graph', $json, '/javascript/bar_graph.js');
        $this->js_call('dropdown_fix', '', '/javascript/dropdown_fix.js');
    }
}