<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Pivotal\Cycle\Models\CycleInterface;
use Pivotal\Department\Models\Department;
use Pivotal\Department\Models\DepartmentInterface;
use Pivotal\Report\Repositories\QuestionBreakdownReportRepository;
use Pivotal\Survey\Models\Survey;
use Carbon\Carbon;
use \Illuminate\Auth\UserInterface;
use Pivotal\Survey\Models\Assessment;
use Pivotal\User\Models\User;

class CycleReportCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cycle:report';

	protected $refresh = false;

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate reports for finished cycle surveys';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		\Log::debug('CycleReportCommand->fire()');

		ini_set('memory_limit','-1');
		ini_set('max_execution_time','-1');

		$this->report = new QuestionBreakdownReportRepository();
		$cycle = new Cycle();
		$days = $this->option('days');
		$refresh = $this->option('refresh');
		$this->refresh = boolval($refresh);
		$endDateStart = \Carbon\Carbon::now()->subDays($days);
		$cycles = $cycle->where('end_date', '<', \DB::raw('NOW()'))->where('end_date', '>=', $endDateStart->format('Y-m-d'))->get();

		if (count($cycles) > 0) {
			echo 'Start at '.date('Y-m-d H:i:s');
			echo "\n";
			echo 'All cycles count '.count($cycles);
			echo "\n";
			$done = [];
			$cyclesCounts = 0;
			foreach ($cycles as $c) {
				$cyclesCounts++;
				echo "\n";
				echo 'cycle '.$cyclesCounts.' id#'.$c->id.' at '.date('Y-m-d H:i:s');
				echo "\n";
				foreach ($c->classes as $course) {
					$teacher = $course->teacher;
					$department = $course->department;
					$school = $teacher->school;
					if (!$school) {
						continue;
					}
					$principal = $school->admin();

					$teacherKey = 'teacher_page_' . $teacher->id . '_' . $c->id;
					if (!isset($done[$teacherKey])) {
						$result = false;
						try {
							$result = $this->teacher_page($teacher, $c);
						} catch (\Exception $e) {
							echo 'teacherKey '.$teacherKey.' failed with error '.$e->getMessage();
							echo "\n";
							\Log::error('teacherKey '.$teacherKey.' failed with error '.$e->getMessage());
						}
						if (!$result) {
							\Log::error('teacher_report failed');
							$done[$teacherKey] = false;
							echo '-';
						} else {
							$done[$teacherKey] = true;
							echo '+';
						}
					}


					if ($department->heads->count() > 0) {
						foreach ($department->heads as $head) {
							$departmentKey = 'department_page_' . $department->id . '_' . $c->id . '_' . $head->id;
							if (!isset($done[$departmentKey])) {
								$result = false;
								try {
									$result = $this->department_head_page($department, $c, $head);
								} catch (\Exception $e) {
									echo 'departmentKey '.$departmentKey.' failed with error ';
									//echo 'departmentKey '.$departmentKey.' failed with error '.$e->getMessage();
									echo "\n";
									\Log::error('departmentKey '.$departmentKey.' failed with error '.$e->getMessage());
								}
								if (!$result) {
									\Log::error('department_head_report failed');
									$done[$departmentKey] = false;
									echo '-';
								} else {
									$done[$departmentKey] = true;
									echo '+';
								}
							}
						}
					}

					if ($principal) {
						$principalKey = 'principal_page_' . $principal->id . '_' . $c->id;
						if (!isset($principalKey)) {
							$result = false;
							try {
								$result = $this->principal_page($principal, $c, 1);
							} catch (\Exception $e) {
								//echo 'principalKey '.$principalKey.' mode 1 failed with error '.$e->getMessage();
								echo 'principalKey '.$principalKey.' mode 1 failed with error ';
								echo "\n";
								\Log::error('principalKey '.$principalKey.' mode 1 failed with error '.$e->getMessage());
							}
							if (!$result) {
								\Log::error('principal_report_1 failed');
								$done[$principalKey] = false;
								echo '-';
							} else {
								$done[$principalKey] = true;
								echo '+';
							}
							try {
								$result = $this->principal_page($principal, $c, 2);
							} catch (\Exception $e) {
								//echo 'principalKey '.$principalKey.' mode 2 failed with error '.$e->getMessage();
								echo 'principalKey '.$principalKey.' mode 2 failed with error ';
								echo "\n";
								\Log::error('principalKey '.$principalKey.' mode 2 failed with error '.$e->getMessage());
							}
							if (!$result) {
								\Log::error('principal_report_2 failed');
								$done[$principalKey.'_2'] = false;
								echo '-';
							} else {
								$done[$principalKey.'_2'] = true;
								echo '+';
							}
						}
					}
				}
			}
			echo "\n";
			echo 'End at '.date('Y-m-d H:i:s');
		}
	}


	public function teacher_page(UserInterface $teacher, CycleInterface $cycle, $mode=2) {

		$this->report = new QuestionBreakdownReportRepository();
		$this->report
			->setTeacher($teacher)
			->setTargetCycle($cycle)
			->load('QuestionBreakdownReport');

		$data['report_title'] = $teacher->school->name;
		$data['report_subtitle'] = 'Teacher Question Breakdown';
		$data['report_cycletitle'] = $cycle->name . ' ' . $cycle->end_date;

		if ($this->report->getEntity()->id && isset($this->report->getData()['chartdata'])) {
			if ($this->refresh) {
				$this->report->getEntity()->delete();
			} else {
				return true;
			}
		}

		if(!in_array($cycle->id, $teacher->cycles->lists('id')))
		{
			return false;
		}

		$cycleYear = \DateTime::createFromFormat('d/m/Y', $cycle->start_date)->format('Y');

		$school = $teacher->school;

		$now = \Carbon\Carbon::createFromTimestamp(time());
		$enddate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date.' 23:59');
		if( $enddate->gt($now)) {
			return false;
		}

		$data['page'] = 'quick_look';
		$data['subheader_bold'] = 'Detailed Question Breakdown';
		$data['subheader'] = ', '.$teacher->name;
		$data['headertext'] = 'Distribution of scores for each question';
		$data['header'] = 'Teacher\'s Question Breakdown for classes of '.$cycle->name;
		$data['cycle'] = $cycle;
		$data['teacher_id'] = $teacher->id;
		$data['additional_info'] = '
                <p>This report provides a breakdown of results for every survey question, for each Class you teach.</p>
                <p>The 25 questions are grouped under the five Australian Professional Standards for Teachers.  At the top of each standard, you can see the highest and lowest scoring Class, and their average scores across that standard, where 1=strongly disagree and 5=strongly agree.</p>
                <p>For each question, you can see the school\'s average score - displayed both in an orange circle, and as a orange dotted line on the bar graph "Average of each Class".  Your average score per question, across all Classes, is shown in a purple box.  The bar graph "Average of each Class" tells you each individual Class score per question. Hover your mouse over columns on the bar graph "Average of each Class", to see the numerical score of each Class.</p>
                <p>The "Distribution of scores" graph indicates the number of your students, across all your Classes, who responded at each point on the scale (from "Strongly Disagree" (1) through to "Strongly Agree" (5)). The numbers at the top of each column tell you the number of student responses at each level.</p>
                <p>Note that where a Class has fewer than five students, the data will not be revealed to protect the anonymity of the students (the relevant section will read "N/A").</p>
                ';

		$limedata = new \LimeData();
		$school_survey_ids = array();
		$teacher_survey_ids = array();
		$class_names = array();

		foreach ($cycle->classes as $class) {
			$school_survey_ids[] = $class->pivot->limesurvey_id;
			if ($class->teacher_id == $teacher->id) {
				$teacher_survey_ids[$class->code] = $class->pivot->limesurvey_id;
				$class_names[$class->code] = $class->name;
			}
		}

		$responses = 0;
		foreach ($teacher_survey_ids as $survey_ids) {
			$responses += $limedata->count_survey_responses($survey_ids);
		}

		if ($responses < Utils::$responses_threshold) {
			return false;
		}


		$questiondata = Survey::with('questions')->first()['questions'];

		$statistics = $limedata->get_surveys_statistics($teacher_survey_ids);

		$question_groups = array();
		for ($i = 0; $i < 5; $i++) {
			$question_groups['question_group'.($i+1)] = array();
			$question_groups['question_group'.($i+1)]['standard_number'] = ($i+1);
			$question_groups['question_group'.($i+1)]['standard'] = Utils::$standards[$i];

			$questions = array();
			for ($j = 0; $j < 5; $j++) {
				$question = array();
				$question['number'] = 'Q'.(($i * 5) + $j + 1);
				$question['text'] = $questiondata[($i * 5) + $j]->question;


				$self_assessment = Assessment::where('cycle_id','=',$cycle->id)->where('teacher_id','=',$teacher->id)->orderBy('created_at', 'DESC');

				if($self_assessment->first())
				{
					$key = ($i * 5) + $j + 1;
					$self_assessment = $self_assessment->first();
					$question['self_assessment'] = $self_assessment->getQuestionValue($key);

				}

				// previous average
				if ($this->report->getTeacher()->surveys()->previous()) {
					$question['average3_label'] = 'Teacher';
					$question['average3_value'] = number_format($this->report->getTeacher()->surveys()->previous()->responses()->answers(($i * 5) + $j + 1)->average(), 1);
				}

				// average
				$question['average2_label'] = 'School';
				$question['average2_value'] = number_format($this->report->getSchool()->surveys()->current()->responses()->answers(($i * 5) + $j + 1)->average(), 1);

				$question['average1_label'] = 'Teacher';
				$question['average1_value'] = number_format($this->report->getTeacher()->surveys()->current()->responses()->answers(($i * 5) + $j + 1)->average(), 1);

				$question['total_responses'] = $this->report->getTeacher()->surveys()->current()->responses()->answers(($i * 5) + $j + 1)->count();


				$question['chart_html'] = $this->quicklook_draw_right_chart_svg($this->report->getTeacher()->surveys()->current()->responses()->answers(($i * 5) + $j + 1)->byValueCount(), $question['total_responses']);


				$questions[]= $question;
			}
			$question_groups['question_group'.($i+1)]['questions'] = $questions;
		}

		$data['question_groups'] = $question_groups;

		//Average of each class
		$data['question_content2'] = 'Average of each Class';
		$data['aggr_type'] = 'Class';

		$class_averages = array();

		foreach ($teacher_survey_ids as $class => $survey_ids) {
			$class_averages[$class] = $limedata->get_survey_question_average(array($survey_ids));
		}


		$chartdata = array();
		$chartdata['aggr_type'] = 'Class average';
		$chartdata['tooltips'] = $class_names;

		for ($i = 0; $i < 25; $i++) {
			$qnumber = 'Q'.($i+1);
			$chartdata[$qnumber] = array();
			$chartdata[$qnumber]['linevalue'] = number_format($this->report->getSchool()->surveys()->current()->responses()->answers($i + 1)->average(), 1);

			foreach($this->report->getTeacher()->surveys()->current()->byCourse() as $course)
			{
				if ($course->start_year != $cycleYear) {
					continue;
				}
				$chartdata[$qnumber]['series'][$course->code] = $course->surveys()->responses()->answers($i+1)->average();
			}

			if ($this->report->getTeacher()->surveys()->previous()) {
				foreach ($this->report->getTeacher()->surveys()->previous()->byCourse() as $course) {
					if ($course->start_year != $cycleYear) {
						continue;
					}
					$chartdata[$qnumber]['previous_series'][$course->code] = $course->surveys()->responses()->answers($i + 1)->average();
				}
			}

			foreach ($chartdata[$qnumber]['series'] as $class => $val) {
				if ($val == max($chartdata[$qnumber]['series'])) {
					$chartdata[$qnumber]['color'][$class] = '#736699';
				} else {
					$chartdata[$qnumber]['color'][$class] = '#B8B0CC';
				}
			}
		}

		$data['chartdata'] = $chartdata;

		$highest = array();
		$lowest = array();
		$totals = array();
		$count = 0;
		$standard = 0;

		foreach ($chartdata as $qnumber => $group) {
			if (in_array($qnumber, array('aggr_type', 'tooltips'))) {
				continue;
			}
			foreach ($group['series'] as $aggr => $val) {
				if ($val == 0) {
					continue;
				}
				if (!empty($totals[$aggr])) {
					$totals[$aggr] += $val;
				} else {
					$totals[$aggr] = $val;
				}
			}
			$count += 1;
			if ($count == 5) {
				$standard += 1;
				$highest[$standard] = array(implode(', ', array_keys($totals, max($totals))), number_format((max($totals) / 5), 1));
				$lowest[$standard] = array(implode(', ', array_keys($totals, min($totals))), number_format((min($totals) / 5), 1));
				$count = 0;
				$totals = array();
			}
		}

		$data['high_standards'] = $highest;
		$data['low_standards'] = $lowest;

		$data['report_title'] = $teacher->school->name;
		$data['report_subtitle'] = 'Teacher Question Breakdown';
		$data['report_cycletitle'] = $cycle->name . ' ' . $cycle->end_date;

		$this->report->setData($data);
		$this->report->save();

		unset($this->report);

		return true;

	}

	public function department_head_page(DepartmentInterface $department, CycleInterface $cycle, User $teacher, $mode=1) {

		$this->report = new QuestionBreakdownReportRepository();

		$this->report
			->setDepartment($department)
			->setTargetCycle($cycle)
			->setTeacher($teacher)
			->load('QuestionBreakdownReportDepartmentHead');

		$data['report_title'] = $department->school->name;
		$data['report_subtitle'] = $department->name . ' Question Breakdown Report';
		$data['report_cycletitle'] = $cycle->name . ' ' . $cycle->end_date;

		if ($this->report->getEntity()->id && isset($this->report->getData()['chartdata'])) {
			if ($this->refresh) {
				$this->report->getEntity()->delete();
			} else {
				return true;
			}
		}

		$now = \Carbon\Carbon::createFromTimestamp(time());
		$enddate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date.' 23:59');
		if( $enddate->gt($now)) {
			return false;
		}

		$cycleYear = \DateTime::createFromFormat('d/m/Y', $cycle->start_date)->format('Y');

		$data['page'] = 'quick_look';
		$data['subheader_bold'] = 'Detailed Question Breakdown';
		$data['subheader'] = ', '.$department->name;
		$data['headertext'] = 'Distribution of scores for each question, by Year Level';
		$data['header'] = 'Head of Department\'s Question Breakdown for '.$department->name;
		$data['cycle'] = $cycle;
		$data['additional_info'] = '
                <p>This report provides a breakdown of results for every survey question, for each Year level within your Department.</p>
                <p>The 25 questions are grouped under the five Australian Professional Standards for Teachers.  At the top of each Standard, you can see the highest and lowest scoring Year level within your Department, and their average scores across that Standard, where 1 = strongly disagree and 5 = strongly agree.</p>
                <p>For each question, you can see the school\'s average score - displayed both in a orange circle, and as a orange dotted line on the bar graph "Average of each Year level".  The Department\'s average score per question, across all Teachers and all Classes, is shown in a purple box.  The bar graph "Average of each Year level" tells you each individual Year level\'s score per question. Hover over columns on the bar graph "Average of each Year level", to see the numerical average score for each Year level.</p>
                <p>The "Distribution of scores" graph indicates the number of students who responded at each point on the scale (from "Strongly Disagree" (1) through to "Strongly Agree" (5)).   The numbers at the top of each column tell you the number of student responses at each level.</p>
                <p>Note that where a year level in your Department has fewer than three teachers, the data will not be revealed to protect the anonymity of the teachers (the relevant section will read "N/A").</p>
                ';
		$limedata = new \LimeData();
		$yearlevels = array();
		$school_survey_ids = array();
		$department_surveys = array();

		foreach ($cycle->classes as $class) {
			if ($class->start_year != $cycleYear) {
				continue;
			}
			$school_survey_ids[] = $class->pivot->limesurvey_id;
			if ($class->department_id == $department->id) {
				$c = $limedata->count_survey_responses($class->pivot->limesurvey_id);
				if ($c > 0) {
					$department_surveys[$class->id] = $class->pivot->limesurvey_id;
					$yearlevels[$class->year_level]['teachers'][$class->teacher->name] = 1;
					$yearlevels[$class->year_level]['surveys'][$class->id] = $class->pivot->limesurvey_id;
				}
			}
		}

		$teacher_count = 0;
		foreach ($yearlevels as $aggr => $classdata) {
			if (count($classdata['teachers']) < Utils::$teacher_threshold) {
				unset($yearlevels[$aggr]['surveys']);
			} else {
				$teacher_count += count($classdata['teachers']);
			}
		}

		$responses = 0;

		foreach ($department_surveys as $survey_id) {
			$responses += $limedata->count_survey_responses($survey_id);
		}

		if ($responses < Utils::$responses_threshold || $teacher_count < Utils::$teacher_threshold) {
			return false;
		}

		$questiondata = Survey::with('questions')->first()['questions'];
		$statistics = $limedata->get_surveys_statistics($department_surveys);

		$department_survey_statistics = $limedata->get_survey_question_average($department_surveys);

		$surveysList = $this->report->getDepartment()->surveys();
		foreach ($teacher->surveys as $s) {
			$surveysList->getEntity()->add($s);
		}
		$surveysListUniqued = $surveysList->getEntity()->unique();
		$surveysList->setEntity($surveysListUniqued);

		$question_groups = array();

		for ($i = 0; $i < 5; $i++) {
			$question_groups['question_group'.($i+1)] = array();
			$question_groups['question_group'.($i+1)]['standard_number'] = ($i+1);
			$question_groups['question_group'.($i+1)]['standard'] = Utils::$standards[$i];

			$questions = array();
			for ($j = 0; $j < 5; $j++) {
				$question = array();
				$question['number'] = 'Q'.(($i * 5) + $j + 1);
				$question['text'] = $questiondata[($i * 5) + $j]->question;

				// average
				if ($surveysList->previous()) {
					$question['average3_label'] = 'Dept.'; //
					$question['average3_value'] = number_format($this->report->getDepartment()->surveys()->previous()->responses()->answers(($i * 5) + $j + 1)->average(), 1);
				}


				$question['average2_label'] = 'School';
				$question['average2_value'] =  number_format($this->report->getSchool()->surveys()->current()->responses()->answers(($i * 5) + $j + 1)->average(), 1);

				$question['average1_label'] = 'Dept.';
				$question['average1_value'] = number_format($this->report->getDepartment()->surveys()->current()->responses()->answers(($i * 5) + $j + 1)->average(), 1);

				$question['total_responses'] = $surveysList->current()->responses()->answers(($i * 5) + $j + 1)->count();

				$question['chart_html'] = $this->quicklook_draw_right_chart_svg($surveysList->current()->responses()->answers(($i * 5) + $j + 1)->byValueCount(), $question['total_responses']);

				$questions[]= $question;
			}
			$question_groups['question_group'.($i+1)]['questions'] = $questions;
		}

		$data['question_groups'] = $question_groups;


		//Average of year level
		$data['question_content2'] = 'Average of each Year Level in '.$department->name;
		$data['aggr_type'] = 'Year Level';

		$average_by_year = array();
		$statistics_by_year = array();
		foreach ($yearlevels as $year => $surveys) {
			if (!empty($surveys['surveys'])) {
				$average_by_year[$year] = $limedata->get_survey_question_average($surveys['surveys']);
			} else {
				$average_by_year[$year] = array();
			}
		}

		ksort($average_by_year);

		$chartdata = array();
		$chartdata['aggr_type'] = 'Year average';
		$chartdata['tooltips'] = array();

		for ($i = 0; $i < 25; $i++) {
			$qnumber = 'Q'.($i+1);
			$chartdata[$qnumber] = array();
			$chartdata[$qnumber]['linevalue'] = number_format($this->report->getSchool()->surveys()->current()->responses()->answers($i + 1)->average(), 1);

			foreach ($surveysList->byYear() as $year) {
				if ($year->surveys()->current()) {
					$average = $year->surveys()->current()->responses()->answers($i + 1)->average();
					if ($average > 0) {
						if (!$year->teacher_threshold) {
							$average = null;
						}
						$chartdata['tooltips']['Year ' . $year->id] = 'Year ' . $year->id;
						$chartdata[$qnumber]['series']['Year ' . $year->id] = $average;
					}
				}
				if ($year->surveys()->previous()) {
					$average = $year->surveys()->previous()->responses()->answers($i + 1)->average();
					if ($average > 0 && isset($chartdata[$qnumber]['series']['Year ' . $year->id])) {
						$chartdata['tooltips']['Year ' . $year->id] = 'Year ' . $year->id;
						$chartdata[$qnumber]['previous_series']['Year ' . $year->id] = $average;
					}
				}
			}

			if (!isset($chartdata['tooltips'])) {
				return false;
			}
			if (isset($chartdata[$qnumber]['series'])) {
				foreach ($chartdata[$qnumber]['series'] as $year => $val) {
					if ($val == max($chartdata[$qnumber]['series'])) {
						$chartdata[$qnumber]['color'][$year] = '#736699';
					} else {
						$chartdata[$qnumber]['color'][$year] = '#B8B0CC';
					}
				}
			}
		}

		$data['chartdata'] = $chartdata;

		$highest = array();
		$lowest = array();
		$totals = array();
		$count = 0;
		$standard = 0;

		foreach ($chartdata as $qnumber => $group) {
			if (in_array($qnumber, array('aggr_type', 'tooltips'))) {
				continue;
			}
			if (isset($group['series'])) {
				foreach ($group['series'] as $aggr => $val) {
					if ($val == 0) {
						continue;
					}
					if (!empty($totals[$aggr])) {
						$totals[$aggr] += $val;
					} else {
						$totals[$aggr] = $val;
					}
				}
			}
			$count += 1;
			if ($totals && $count == 5) {
				$standard += 1;
				$highest[$standard] = array(implode(', ', array_keys($totals, max($totals))), number_format((max($totals) / 5), 1));
				$lowest[$standard] = array(implode(', ', array_keys($totals, min($totals))), number_format((min($totals) / 5), 1));
				$count = 0;
				$totals = array();
			}
		}

		$data['high_standards'] = $highest;
		$data['low_standards'] = $lowest;


		$this->report->setData($data);
		$this->report->save();

		unset($this->report);

		return true;
	}

	public function principal_page(UserInterface $teacher, CycleInterface $cycle, $mode=1) {
		$this->report = new QuestionBreakdownReportRepository();

		$this->report
			->setSchool($teacher->school)
			->setTargetCycle($cycle);

		if($mode == 1)
		{
			$this->report->load('QuestionBreakdownReportDepartment');
		}
		if($mode == 2)
		{
			$this->report->load('QuestionBreakdownReportYear');
		}

		$data['report_title'] = $teacher->school->name;
		$data['report_subtitle'] = 'School Summary Question Breakdown';
		$data['report_cycletitle'] = $cycle->name . ' ' . $cycle->end_date;

		if ($this->report->getEntity()->id && isset($this->report->getData()['chartdata'])) {
			if ($this->refresh) {
				$this->report->getEntity()->delete();
			} else {
				return true;
			}
		}

		$school = $teacher->school;

		$now = \Carbon\Carbon::createFromTimestamp(time());
		$enddate = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $cycle->end_date.' 23:59');
		if( $enddate->gt($now)) {
			return false;
		}

		$data['page'] = 'quick_look';
		$data['subheader_bold'] = 'Detailed Question Breakdown';
		$data['display_mode'] = $mode == 2 ? 1 : 2;
		$data['display_mode_text'] = $mode == 2 ? 'Department' : 'Year Level';
		$data['subheader'] = ', '.$school->name;
		$data['headertext'] = 'Distribution of scores for each question by Department/Year Level';
		$data['header'] = 'Principal\'s Question Breakdown for '.$school->name;
		$data['cycle'] = $cycle;
		$data['teacher_id'] = $teacher->id;
		$data['additional_info'] = '
                <p>This report provides a breakdown of results for every survey question, either for each department or for each year-level.</p>
                <p>At the top of the page, select the button "Show Department Breakdown" or "Show Year Level Breakdown" to reveal results either by Department or by Year level, for every question.</p>
                <p>The 25 questions are grouped under the five Australian Professional Standards for Teachers.  At the top of each standard, you can see the highest and lowest scoring Departments/Year levels, and their average scores across that standard, where 1=strongly disagree and 5=strongly agree.</p>
                <p>For each question, you can see the school\'s average score - displayed both in a orange circle, and as a orange dotted line on the bar graph "Average of each Department/Year level".  Hover over columns on the bar graph "Average of each Department/Year level", to see the numerical score of each Department/Year level.</p>
                <p>The "Distribution of scores" graph indicates the number of students who responded at each point on the scale (from "Strongly Disagree" through to "Strongly Agree"). </p>
                <p>Note that where a Department or year level has fewer than three teachers, the data will not be revealed to protect the anonymity of the teachers.</p>
                ';

		$limedata = new \LimeData();
		$school_survey_ids = array();
		$aggr_surveys = array();

		foreach ($cycle->classes as $class) {
			$school_survey_ids[] = $class->pivot->limesurvey_id;
			if ($mode == 1) {
				$data['aggr_type'] = 'Department';
				$data['question_content2'] = 'Average of each Department';
				$aggr_surveys[$class->department->name]['teachers'][$class->teacher->name] = 1;
				$aggr_surveys[$class->department->name]['surveys'][] = $class->pivot->limesurvey_id;
			} else {
				$data['aggr_type'] = 'Year Level';
				$data['question_content2'] = 'Average of each Year Level';
				$aggr_surveys[$class->year_level]['teachers'][$class->teacher->name] = 1;
				$aggr_surveys[$class->year_level]['surveys'][] = $class->pivot->limesurvey_id;
			}
		}

		$teacher_count = 0;
		foreach ($aggr_surveys as $aggr => $classdata) {
			if (count($classdata['teachers']) < Utils::$teacher_threshold) {
				unset($aggr_surveys[$aggr]['surveys']);
			} else {
				$teacher_count += count($classdata['teachers']);
			}
		}

		$responses = 0;

		foreach($school_survey_ids as $key => $value)
		{
			if($value == null)
			{
				unset($school_survey_ids[$key]);
			}
		}

		foreach ($school_survey_ids as $survey_id) {
			$responses += $limedata->count_survey_responses($survey_id);
		}

		//@todo uncomment the next lines
		if ($responses < Utils::$responses_threshold || $teacher_count < Utils::$teacher_threshold) {
			return false;
		}

		$questiondata = Survey::with('questions')->first()['questions'];
		$statistics = $limedata->get_surveys_statistics($school_survey_ids);


		$question_groups = array();
		for ($i = 0; $i < 5; $i++) {
			$question_groups['question_group'.($i+1)] = array();
			$question_groups['question_group'.($i+1)]['standard_number'] = ($i+1);
			$question_groups['question_group'.($i+1)]['standard'] = Utils::$standards[$i];

			$questions = array();
			for ($j = 0; $j < 5; $j++) {
				$question = array();
				$question['number'] = 'Q'.(($i * 5) + $j + 1);
				$question['text'] = $questiondata[($i * 5) + $j]->question;

				// average
				if ($this->report->getSchool()->surveys()->previous()) {
					$question['average3_label'] = 'School';
					$question['average3_value'] = number_format($this->report->getSchool()->surveys()->previous()->responses()->answers(($i * 5) + $j + 1)->average(), 1);

				}

				$question['average2_label'] = 'School';
				$question['average2_value'] = number_format($this->report->getSchool()->surveys()->current()->responses()->answers(($i * 5) + $j + 1)->average(), 1);


				$question['total_responses'] = $this->report->getSchool()->surveys()->current()->responses()->answers(($i * 5) + $j + 1)->count();

				if($question['total_responses'] > 0)
				{
					$question['chart_html'] = $this->quicklook_draw_right_chart_svg($this->report->getSchool()->surveys()->current()->responses()->answers(($i * 5) + $j + 1)->byValueCount(), $question['total_responses']);
				}

				$questions[]= $question;
			}
			$question_groups['question_group'.($i+1)]['questions'] = $questions;
		}
		$data['question_groups'] = $question_groups;

		$cycleYear = \DateTime::createFromFormat('d/m/Y', $cycle->start_date)->format('Y');

		if ($mode == 1) {
			//Set the default and constant chartdata values
			$chartdata = array(
				'tooltips' => array(),
				'aggr_type' => 'Department average'
			);



			//foreach question get the department averages
			for ($i = 0; $i < 25; $i++) {
				$qnumber = 'Q' . ($i + 1);
				$chartdata[$qnumber] = array();
				//Get School Average for the question
				$chartdata[$qnumber]['linevalue'] = number_format($this->report->getSchool()->surveys()->current()->responses()->answers($i + 1)->average(), 1);
				//Set the department averages
				foreach ($this->report->getSchool()->surveys()->byDepartment() as $department) {
					$sids = [];
					$classes = $department->classesForYear($cycleYear);

					$teacherClasses = [];
					foreach ($classes as $class) {
						$tResponses = 0;
						$limesurveyId = $class->getLimesurveyId($cycle);
						if ($limesurveyId) {
							$tResponses = $limedata->count_survey_responses($limesurveyId);
						}
						if ($tResponses > 0) {
							$teacherClasses[] = $class->teacher_id;
						}
					}
					$teacherClasses = array_unique($teacherClasses);

					/* remove it for PT-147 fix */
					if (count($teacherClasses) < Utils::$teacher_threshold) {
						continue;
					}

					foreach ($classes as $class) {
						$limesurveyId = $class->getLimesurveyId($cycle);
						$sids[] = $limesurveyId;
					}
					//If we have surveys for the target cycle
					if ($current = $department->surveys()->current()) {
						$average = $current->responsesInSids($sids)->answers($i + 1)->average();
						if ($average > 0) {
							$chartdata['tooltips'][$department->name] = $department->name;
							// uncomment it for PT-147 fix
							/*
                            if (count($teacherClasses) < Utils::$teacher_threshold) {
                                $average = null;
                            }
                            */
							$chartdata[$qnumber]['series'][$department->name] = $average;
						}
					}

					//If we have previous surveys for the department
					if ($previous = $department->surveys()->previous()) {
						$prev_average = $previous->responsesInSids($sids)->answers($i + 1)->average();
						if ($prev_average > 0) {
							$chartdata['tooltips'][$department->name] = $department->name;
							$chartdata[$qnumber]['previous_series'][$department->name] = $prev_average;
						}
					}

				}

				//dd($chartdata);

				if (!isset($chartdata['tooltips'])) {
					return false;
				}

				if (isset($chartdata[$qnumber]['series'])) {
					foreach ($chartdata[$qnumber]['series'] as $dept => $val) {
						if ($val == max($chartdata[$qnumber]['series'])) {
							$chartdata[$qnumber]['color'][$dept] = '#736699';
						} else {
							$chartdata[$qnumber]['color'][$dept] = '#B8B0CC';
						}
					}
				}

			}


		}



		if ($mode == 2) {
			$chartdata = array(
				'tooltips' => array(),
				'aggr_type' => 'Year average'
			);

			for ($i = 0; $i < 25; $i++) {
				$qnumber = 'Q' . ($i + 1);
				$chartdata[$qnumber] = array();
				$chartdata[$qnumber]['linevalue'] = number_format($this->report->getSchool()->surveys()->current()->responses()->answers($i + 1)->average(), 1);
				foreach ($this->report->getSchool()->surveys()->byYear() as $year) {
					//If we have surveys for the target cycle
					if ($year->surveys()->current()) {
						$average = $year->surveys()->current()->responses()->answers($i + 1)->average();
						if ($average > 0) {
							$chartdata['tooltips']['Year ' . $year->id] = 'Year ' . $year->id;
							$chartdata[$qnumber]['series']['Year ' . $year->id] = $average;
						}
					}
					//If we have surveys for the target cycle
					if ($year->surveys()->previous()) {
						$average = $year->surveys()->previous()->responses()->answers($i + 1)->average();
						if ($average > 0) {
							$chartdata['tooltips']['Year ' . $year->id] = 'Year ' . $year->id;
							$chartdata[$qnumber]['previous_series']['Year ' . $year->id] = $average;
						}
					}
				}

				if (!isset($chartdata['tooltips'])) {
					return false;
				}

				foreach ($chartdata[$qnumber]['series'] as $year => $val) {
					if ($val == max($chartdata[$qnumber]['series'])) {
						$chartdata[$qnumber]['color'][$year] = '#736699';
					} else {
						$chartdata[$qnumber]['color'][$year] = '#B8B0CC';
					}
				}
			}
		}

		$data['chartdata'] = $chartdata;

		$highest = array();
		$lowest = array();
		$totals = array();
		$count = 0;
		$standard = 0;

		foreach ($chartdata as $qnumber => $group) {
			if (in_array($qnumber, array('aggr_type', 'tooltips'))) {
				continue;
			}
			if (isset($group['series'])) {
				foreach ($group['series'] as $aggr => $val) {
					if ($val == 0) {
						continue;
					}
					if (!empty($totals[$aggr])) {
						$totals[$aggr] += $val;
					} else {
						$totals[$aggr] = $val;
					}
				}
				$count += 1;
				if ($count == 5) {
					$standard += 1;
					$highest[$standard] = array(implode(', ', array_keys($totals, max($totals))), number_format((max($totals) / 5), 1));
					$lowest[$standard] = array(implode(', ', array_keys($totals, min($totals))), number_format((min($totals) / 5), 1));
					$count = 0;
					$totals = array();
				}
			}
		}

		$data['high_standards'] = $highest;
		$data['low_standards'] = $lowest;


		$this->report->setData($data);
		$this->report->save();

		unset($this->report);

		return true;
	}

	/**
	 * Draws the distribution chart for a Quick Look page
	 * @param array $responses (score => number of responses)
	 * @param int $total total responses
	 * @return string html of chart
	 */

	public function quicklook_draw_right_chart ($responses, $total, $negative = false) {
		$html = '<ul class="quicklook_dist_chart">';
		$left = 0;
		$max = max($responses);
		foreach ($responses as $response) {
			$height = floor(($response / $total) * 100);
			if ($response == $max) {
				$html .= "<li class=\"highest\" style=\"height:$height%;left:$left%\"><div class=\"dist_response\">$response</div></li>";
			} else {
				$html .= "<li style=\"height:$height%;left:$left%\"><div class=\"dist_response\">$response</div></li>";
			}

			$left += 20;
		}
		if ($negative) {

		} else {
			$html .= '</ul><div class="chart_dist_left">Strongly disagree</div><div class="chart_dist_right">Strongly agree</div>';
		}

		return $html;
	}

	public function quicklook_draw_right_chart_svg ($responses, $total) {
		$svg = '';
		$left = 10;
		$maxHeight = 90;
		$max = max($responses);
		foreach ($responses as $response) {
			$height = 5+round(90*$response/$total);

			$svg .= "<text x='".($left+8)."%' y='".(100+$maxHeight-$height-5)."px' text-anchor='middle'>".$response."</text>";
			if ($response == $max) {
				$svg .= "<rect x='".$left."%' y='".(100+$maxHeight-$height)."px' width='15%' height='".$height."px' fill='#73659B'/>";
			} else {
				$svg .= "<rect x='".$left."%' y='".(100+$maxHeight-$height)."px' width='15%' height='".$height."px' fill='#B8B0CC'/>";
			}

			$left += 16;
		}

		return $svg;
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('refresh', null, InputOption::VALUE_OPTIONAL, 'Force to refresh cached reports', 0),
			array('days', null, InputOption::VALUE_OPTIONAL, 'Chech x says for finished reports', 1),
		);
	}


}
