<?php

/**
 * Controller for the quicklook page
 *
 * @package
 * @subpackage
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

class QuicklookSummaryBak extends BaseController {

    public function show_page(User $teacher, Department $department) {
        $limedata = new LimeData();

        $school = School::find($department->school_id);

        $teacher_survey_ids = User::get_survey_ids($teacher->id);
        $teacher_statistics = $limedata->get_survey_question_average($teacher_survey_ids);

        // get the question text
        $questions = $limedata->get_survey_questions(reset($teacher_survey_ids), LimeData::QUESTION_INDEX_COUNT);

        // get the chart for average
        foreach ($questions as $questionindex => $question) {
            $barchart = new HorizontalBar($teacher_statistics[$questionindex]);
            $question->youraverage = $barchart->output();
        }

        // get the chart for quintile
        $teachers = $department->teachers;
        foreach ($teachers as $teacher_id => $teacher) {
            $sids = User::get_survey_ids($teacher_id);
            $teachers[$teacher_id] = $limedata->get_survey_question_average($sids);
        }
        $question_averages = $this->combine_teacher_question_average($teachers);
        foreach ($question_averages as $questionindex => $averagevalues) {
            $quintile = Utils::compute_quintile($teacher_statistics[$questionindex], $averagevalues);
            $dotbar = new DotBar($quintile);
            $questions[$questionindex]->comparetoothers = $dotbar->output();
        }

        $data = array();
        $data['questions'] = $questions;
        $data['page'] = 'quick_look_summary'; // id of the body tag
        $data['header'] = 'Semester 1, 2015';
        $data['subheader_bold'] = $teacher->name;
        $data['subheader'] = ', '.$school->name;

        return $this->get_view('quick_look_summary', $data);
    }

    /**
     * Combine the question average for each teachers to an array of averages for each questions
     * @param array $teachers the array of averages for each teacher, e.g.
     *      array(
     *          teacher_id1 => array(
     *              questionindex1 => teacher1_question_average1,
     *              questionindex2 => teacher1_question_average2,
     *              ...
     *          ),
     *          teacher_id2 => array(
     *              ...
     *          ),
     *          ...
     *      )
     * @return array array of each average, e.g.
     *      array(
     *          questionindex1 => array(teacher1_question_average1, teacher2_question_average1, ...),
     *          questionindex2 => array(teacher1_question_average1, teacher2_question_average1, ...),
     *      )
     */
    private function combine_teacher_question_average($teachers) {
        $questions_averages_array = array();
        foreach ($teachers as $teacher_id => $questions_avg) {
            foreach ($questions_avg as $question_idx => $avg) {
                $questions_averages_array[$question_idx][$teacher_id]= $avg;
            }
        }
        return $questions_averages_array;
    }
}
