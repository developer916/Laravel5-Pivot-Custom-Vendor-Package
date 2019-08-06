<?php namespace Pivotal\Report\Controllers;


use Illuminate\Auth\UserInterface;
use Pivotal\Cycle\Models\CycleInterface;
use Pivotal\Department\Models\DepartmentInterface;
use \Redirect;
use \Utils;


class ScatterPlotController extends BaseReportController {
    public function principal_page (UserInterface $teacher, CycleInterface $cycle, $mode = 1) {

        $school = $teacher->school;

        $now = \Carbon\Carbon::createFromTimestamp(time());
        $enddate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date.' 23:59');
        if( $enddate->gt($now)) {
            return Redirect::to("/school/view/$school->id")->with('error', 'Cycle still ongoing');
        }

        $data = array();
        $chartdata = array();
        $limedata = new \LimeData();

        $data['page'] = 'scatter_plot';
        $data['subheader_bold'] = 'Scatter Report';
        $data['display_mode'] = $mode == 2 ? 1 : 2;
        $data['display_mode_text'] = $mode == 2 ? 'Department' : 'Year Level';
        $data['subheader'] = ', '.$school->name;

        $data_type = ($mode == 1) ? 'Department' : 'Year level';

        $data['headertext'] = 'Scores for each question by Department/Year level';
        $data['header'] = 'Principal\'s Scatter Report for '.$cycle->name;
        $data['cycle'] = $cycle;
        $data['teacher_id'] = $teacher->id;
        $data['additional_info'] = '
                <p>This scatter plot is useful for seeing whole school trends per question, indicating systemic strengths and weaknesses.  It will show you the range of your Departments/year levels\' average responses per question.</p>
                <p>It will also allow you to isolate and compare a single Department/Year level across all questions, relative to the school average and other Departments/Year levels.</p>
                <p>At the top of the page, select the button "Show Department breakdown" or "Show Year Level breakdown" to reveal results either by Department or by Year level, for every Standard.</p>
                <p>The orange dot represents the school average score for that question, where 1=strongly disagree and 5=strongly agree.  Hover your mouse over a point on the graph to see the name of that Department/Year level, and their numerical average score for that question.</p>
                <p>Hover over the question number at the bottom of the graph to reveal the text of that question.</p>
                <p>You can click on a single point on the scatter plot to link up that Department/Year level\'s scores with a line, across all questions.</p>
                <p>You can click on any Department/Year level in the legend to show that Department/Year level\'s results in the graph, or to remove them from the graph.</p>
                <p>This graph is most useful when viewed and manipulated online, and its utility as a printed graph is limited. Note that Departments/Year levels with fewer than three teachers will not be shown on this graph to preserve the anonymity of the teachers.</p>
        ';

        $school_surveys = array();
        $aggr_surveys = array();

        $teacherClasses = [];

        $tt = [];

        foreach ($cycle->classes as $class) {
            $school_surveys[] = $class->pivot->limesurvey_id;
            $tResponses = $limedata->count_survey_responses($class->pivot->limesurvey_id);
            if ($tResponses > 0) {
                $teacherClasses[] = $class->teacher_id;
            }

            if ($mode == 1) {
                if ($tResponses > 0) {
                    $data['data_type'] = 'departments';
                    $aggr_surveys[$class->department->name]['teachers'][$class->teacher->name] = 1;
                    $aggr_surveys[$class->department->name]['surveys'][] = $class->pivot->limesurvey_id;
                }
            } else {
                $data['data_type'] = 'year levels';
                $aggr_surveys['Year ' . $class->year_level]['teachers'][$class->teacher->name] = 1;
                $aggr_surveys['Year ' . $class->year_level]['surveys'][] = $class->pivot->limesurvey_id;
            }
        }

        $teacherClasses = array_unique($teacherClasses);

        foreach ($aggr_surveys as $aggr => $classdata) {
            if (count($classdata['teachers']) < Utils::$teacher_threshold) {
                unset($aggr_surveys[$aggr]);
            }
        }

        $responses = 0;

        foreach ($aggr_surveys as $classdata) {
            foreach ($classdata['surveys'] as $survey_id) {
                $responses += $limedata->count_survey_responses($survey_id);
            }
        }

        if (count($teacherClasses) < Utils::$teacher_threshold) {
            return Redirect::to("/school/view/$school->id")->with('error', 'Report has not yet met survey response number threshold');
        }

        if ($responses < Utils::$responses_threshold) {
            return Redirect::to("/school/view/$school->id")->with('error', 'Report has not yet met survey response number threshold');
        }

        $questiondata = $limedata->get_survey_questions(reset($school_surveys), \LimeData::QUESTION_INDEX_COUNT);

        $school_survey_statistics = $limedata->get_survey_question_average($school_surveys);

        $aggr_survey_averages = array();

        foreach ($aggr_surveys as $aggr => $surveys) {
            $aggr_survey_averages[$aggr] = $limedata->get_survey_question_average($surveys['surveys']);
        }

        $chartdata['series']['data']['school_average']['name'] = 'School Average';
        $chartdata['series']['data']['school_average']['color'] = 'rgba(255,165,0, .5)';
        $chartdata['series']['data']['school_average']['symbol'] = 'circle';
        foreach ($school_survey_statistics as $qnum => $average) {
            $chartdata['series']['data']['school_average']['values'][] = array($qnum+1, number_format($average,1) * 1);
        }

        foreach ($aggr_survey_averages as $aggr => $averages) {
            $chartdata['series']['data'][$aggr]['name'] = $aggr;
            $chartdata['series']['data'][$aggr]['color'] = 'rgba(149,144,190, .6)';
            $chartdata['series']['data'][$aggr]['symbol'] = 'square';
            foreach ($averages as $qnum => $average){
                $chartdata['series']['data'][$aggr]['values'][] = array($qnum+1, number_format($average,1) * 1);
            }
        }


        $data['report_title'] = $teacher->school->name;
        $data['report_subtitle'] = 'School Summary Scatter Graph';
        $data['report_cycletitle'] = $cycle->name . ' ' . $cycle->end_date;

        $data['report_title'] = $teacher->school->name . ' Scatter Report ' . $cycle->end_date;

        $chartdata['standards'] = Utils::$standards;
        $chartdata['questions'] = $questiondata;
        $this->draw_scatter_plot($chartdata);
        $this->invoke_tooltip();
        return $this->get_view('report-scatter_plot', $data);
    }

    public function department_head_page (DepartmentInterface $department, CycleInterface $cycle, $mode = 1) {

        $now = \Carbon\Carbon::createFromTimestamp(time());
        $enddate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date.' 23:59');
        if( $enddate->gt($now)) {
            return Redirect::to("/department/view/$department->id")->with('error', 'Cycle still ongoing');
        }

        $teacher = \Auth::user();

        $data = array();
        $chartdata = array();
        $limedata = new \LimeData();

        $data['page'] = 'scatter_plot';
        $data['subheader_bold'] = 'Scatter Report';
        $data['subheader'] = ', '.$department->name;

        $data_type = 'Year level';

        $data['headertext'] = 'Department\'s score for each question, by Year level';
        $data['header'] = 'Head of Department\'s Scatter Report for '.$cycle->name;
        $data['cycle'] = $cycle;
        $data['additional_info'] = '
                <p>This scatter plot is useful for seeing your Department\'s trends per question, indicating systemic strengths and weaknesses.  It will show you the range of average responses per question, by Year levels within your Department.</p>
                <p>It will also allow you to isolate and compare a single Year level across all questions, relative to the school average and other Year levels.</p>
                <p>The orange dot represents the school average score for that question, where 1 = strongly disagree and 5 = strongly agree.  Hover your mouse over a point on the graph to see the name of that Year level, and their numerical average score for that question.  Hover over the question number at the bottom of the graph to reveal the text of that question.</p>
                <p>You can click on a single point on the scatter plot to link up that Year level\'s scores with a line across all questions.</p>
                <p>You can click on any Year level in the legend to show that Year level\'s results in the graph, or to remove that Year level\'s results from the graph.</p>
                <p>This graph is most useful when viewed and manipulated online, and its utility as a printed graph is limited. Note that Year Levels with fewer than three teachers in your Department will not be shown on this graph to preserve the anonymity of the teachers.</p>
        ';

        $school_surveys = array();
        $department_surveys = array();
        $aggr_surveys = array();

        foreach ($cycle->classes as $class) {
            $school_surveys[] = $class->pivot->limesurvey_id;
            if ($class->department->id == $department->id) {
                $c = $limedata->count_survey_responses($class->pivot->limesurvey_id);
                if ($c > 0) {
                    $data['data_type'] = 'year levels';
                    $aggr_surveys['Year ' . $class->year_level]['teachers'][$class->teacher->name] = 1;
                    $aggr_surveys['Year ' . $class->year_level]['surveys'][] = $class->pivot->limesurvey_id;
                }
            }
            if ($class->department->id == $department->id) {
                $department_surveys[] = $class->pivot->limesurvey_id;
            }
        }

        $aggr_surveys_original = $aggr_surveys;

        foreach ($aggr_surveys as $aggr => $classdata) {
            if (count($classdata['teachers']) < Utils::$teacher_threshold) {
                unset($aggr_surveys[$aggr]);
            }
        }

        $responses = 0;

        foreach ($aggr_surveys as $classdata) {
            foreach ($classdata['surveys'] as $survey_id) {
                $responses += $limedata->count_survey_responses($survey_id);
            }
        }

        if ($responses < Utils::$responses_threshold) {
            if (count($aggr_surveys_original) > 0 && count($aggr_surveys) == 0) {
                $aggr_surveys = array();
            } else {
                return Redirect::to("/department/view/$department->id")->with('error', 'Report has not yet met survey response number threshold');
            }
        }

        $questiondata = $limedata->get_survey_questions(reset($school_surveys), \LimeData::QUESTION_INDEX_COUNT);

        $school_survey_statistics = $limedata->get_survey_question_average($school_surveys);
        $department_survey_statistics = $limedata->get_survey_question_average($department_surveys);

        $aggr_survey_averages = array();

        foreach ($aggr_surveys as $aggr => $surveys) {
            $aggr_survey_averages[$aggr] = $limedata->get_survey_question_average($surveys['surveys']);
        }

        $chartdata['series']['data']['school_average']['name'] = 'School Average';
        $chartdata['series']['data']['school_average']['color'] = 'rgba(255,165,0, .5)';
        $chartdata['series']['data']['school_average']['symbol'] = 'circle';
        foreach ($school_survey_statistics as $qnum => $average) {
            $chartdata['series']['data']['school_average']['values'][] = array($qnum+1, number_format($average,1) * 1);
        }

        $chartdata['series']['data']['department_average']['name'] = 'Department Average';
        $chartdata['series']['data']['department_average']['color'] = 'rgba(0,165,255, .5)';
        $chartdata['series']['data']['department_average']['symbol'] = 'circle';
        foreach ($department_survey_statistics as $qnum => $average) {
            $chartdata['series']['data']['department_average']['values'][] = array($qnum+1, number_format($average,1) * 1);
        }

        foreach ($aggr_survey_averages as $aggr => $averages) {
            $chartdata['series']['data'][$aggr]['name'] = $aggr;
            $chartdata['series']['data'][$aggr]['color'] = 'rgba(149,144,190, .6)';
            $chartdata['series']['data'][$aggr]['symbol'] = 'square';
            foreach ($averages as $qnum => $average){
                $chartdata['series']['data'][$aggr]['values'][] = array($qnum+1, number_format($average,1) * 1);
            }
        }

        $data['report_title'] = $department->school->name;
        $data['report_subtitle'] = $department->name . ' Scatter Graph';
        $data['report_cycletitle'] = $cycle->name . ' ' . $cycle->end_date;

        $chartdata['standards'] = Utils::$standards;
        $chartdata['questions'] = $questiondata;
        $this->draw_scatter_plot($chartdata);
        $this->invoke_tooltip();
        return $this->get_view('report-scatter_plot', $data);
    }

    public function draw_scatter_plot ($data) {
        $this->js_include('https://code.highcharts.com/highcharts.js');
        $this->js_include('https://code.highcharts.com/highcharts-more.js');
        $this->js_include('https://code.highcharts.com/modules/exporting.js');

        $json = $data;

        $this->js_call('draw_scatter_plot', $json, '/javascript/scatter_plot.js');
        $this->js_call('dropdown_fix', '', '/javascript/dropdown_fix.js');
    }
}