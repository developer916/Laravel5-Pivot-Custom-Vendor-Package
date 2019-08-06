<?php
use \Illuminate\Auth\UserInterface;
use \Pivotal\Cycle\Models\CycleInterface;
class ComparisonTableControllerBak extends BaseController {
    public function principal_page (UserInterface $teacher, CycleInterface $cycle, $mode = 1) {
        $data = array();
        $school = $teacher->school;
        $limedata = new LimeData();
        $chartdata = array();

        $now = Carbon\Carbon::createFromTimestamp(time());
        $enddate = Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date.' 23:59');
        if( $enddate->gt($now)) {
            return Redirect::to("/school/view/$school->id")->with('error', 'Cycle still ongoing');
        }

        $data['page'] = 'comparison_table';
        $data['subheader_bold'] = 'Grid Report';
        $data['subheader'] = ', '.$school->name;
        $data['display_mode'] = $mode == 2 ? 1 : 2;
        $data['display_mode_text'] = $mode == 2 ? 'Department' : 'Year Level';
        $data['headertext'] = ' | Department/Year level ranking, by standard';
        $data['header'] = 'Principal\'s Grid Report for '.$school->name;
        $data['cycle'] = $cycle;
        $data['teacher_id'] = $teacher->id;
        $data['additional_info'] = '
                <p>This report shows the rank of each Department/year level, for each Standard.</p>
                <p>At the top of the page, select the button "Show Department breakdown" or "Show Year Level breakdown" to reveal results either by Department or by Year level, for every Standard.</p>
                <p>Hover over any box to reveal the detail of the question and the Department/Year level\'s average score for that Standard.  The school\'s average score for each Standard can be seen along the bottom row.</p>
        ';

        $school_surveys = array();
        $aggr_array = array();
        $aggr_surveys = array();

        foreach ($cycle->classes as $class) {
            $school_surveys[] = $class->pivot->limesurvey_id;
            if ($mode == 1) {
                $aggr_array[$class->department->name]['teachers'][$class->teacher->name] = 1;
                $aggr_array[$class->department->name]['surveys'][] = $class->pivot->limesurvey_id;
            } else {
                $aggr_array['Year '.$class->year_level]['teachers'][$class->teacher->name] = 1;
                $aggr_array['Year '.$class->year_level]['surveys'][] = $class->pivot->limesurvey_id;
            }
        }

        $chartdata['catagories'][] = 'AVERAGE';
        $catagories = array();
        foreach ($aggr_array as $aggr => $classdata) {
            if (count($classdata['teachers']) < Utils::$teacher_threshold) {
                unset($aggr_array[$aggr]);
            } else {
                $catagories[] = $aggr;
            }
        }

        $responses = 0;

        foreach ($aggr_array as $classdata) {
            foreach ($classdata['surveys'] as $survey_id) {
                $responses += $limedata->count_survey_responses($survey_id);
            }
        }

        if ($responses < Utils::$responses_threshold) {
            return Redirect::to("/school/view/$school->id")->with('error', 'Report has not yet met survey response number threshold');
        }

        $school_question_average = $limedata->get_survey_question_average($school_surveys);

        for ($i = 0; $i < 5; $i++) {
            $total = 0;
            $aggr_total = 0;
            for ($j =0; $j < 5; $j++) {
                $total += $school_question_average[$i*5 + $j];
            }
            $school_average = $total/5;

            $chartdata['school_average'][] = number_format($school_average, 1) * 1;
        }

        $results = array();
        foreach ($aggr_array as $aggr => $classdata) {
            $results[$aggr] = $limedata->get_survey_question_average($classdata['surveys']);
        }

        $standard_results = array();
        $cat_averages = array();
        foreach ($results as $aggr => $result) {
            for ($i = 0; $i < 5; $i++) {
                $total = 0;
                $aggr_total = 0;
                for ($j =0; $j < 5; $j++) {
                    $total += $result[$i*5 + $j];
                }
                $standard_average = number_format($total/5, 1) * 1;
                $cat_averages[$aggr] = empty($cat_averages[$aggr]) ? $standard_average : $cat_averages[$aggr] + $standard_average;
                $standard_results[$i][$aggr] = $standard_average;
            }
        }

        asort($cat_averages);
        foreach($cat_averages as $key => $value) {
            $chartdata['catagories'][] = $key;
        }

        $data['chartsize'] = (count($chartdata['catagories']) * 83) + 119;

        for ($i = 0; $i < 5; $i++) {
            $copy = $standard_results[$i];
            arsort($copy);
            asort($standard_results[$i]);
            $lowest = reset($standard_results[$i]);
            $count = 1;
            foreach ($standard_results[$i] as $group => $result) {
                if ($count == count($standard_results[$i])) {
                    // last result of the asc sorted array is the highest
                    $chartdata['series']['data'][] = array($i, array_search($group, $chartdata['catagories']), 5);
                } else if ($count == 1) {
                    // first result of the asc sorted array is the lowest
                    $chartdata['series']['data'][] = array($i, array_search($group, $chartdata['catagories']), 1);
                } else if ($result == $lowest) {
                    // if result is the same as the lowest value, it is also the lowest
                    $chartdata['series']['data'][] = array($i, array_search($group, $chartdata['catagories']), 1);
                } else if ($result == reset($copy)) {
                    // if the result is same as the first result of the desc sorted array, it is also the highest
                    $chartdata['series']['data'][] = array($i, array_search($group, $chartdata['catagories']), 5);
                } else if ($result == $chartdata['school_average'][$i]) {
                    // equals to the average
                    $chartdata['series']['data'][] = array($i, array_search($group, $chartdata['catagories']), 3);
                } else if ($result > $chartdata['school_average'][$i]) {
                    // higher than the average
                    $chartdata['series']['data'][] = array($i, array_search($group, $chartdata['catagories']), 4);
                } else if ($result < $chartdata['school_average'][$i]) {
                    // lower than the average
                    $chartdata['series']['data'][] = array($i, array_search($group, $chartdata['catagories']), 2);
                }
                $count += 1;
            }
        }

        $chartdata['standard_values'] = $standard_results;
        $chartdata['standards'] = Utils::$standards;

        $index = 0;
        foreach ($chartdata['school_average'] as $average) {
            $chartdata['series']['data'][] = array($index,0, 6);
            $index ++;
        }

        $this->draw_comparison_table($chartdata);
        $this->invoke_tooltip();

        return $this->get_view('report-comparison_table', $data);
    }

    public function draw_comparison_table ($data) {
        $this->js_include('http://code.highcharts.com/highcharts.js');
        $this->js_include('http://code.highcharts.com/modules/heatmap.js');
        $this->js_include('http://code.highcharts.com/modules/exporting.js');

        $json = $data;

        $this->js_call('draw_comparison_table', $json, '/javascript/comparison_table.js');
        $this->js_call('dropdown_fix', '', '/javascript/dropdown_fix.js');
    }
}