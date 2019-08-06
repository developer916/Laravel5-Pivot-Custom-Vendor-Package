<?php namespace Pivotal\Csv\Controllers;

use \Input;
use \Pivotal\Csv\Facades\Csv;
use \Pivotal\Csv\Models\SchoolImport;

use Illuminate\Support\Facades\Redirect;
use Pivotal\School\Models\SchoolInterface;

/**
 * File description
 *
 * @package
 * @subpackage
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */
class ImportCSVController extends BaseCsvController
{

    private $required_cols = array('department', 'class', 'code', 'year level', 'number of students', 'teacher', 'role', 'email');
    private $reader;
    private $currentuser;
    private $school;

    /**
     * Display the upload form
     */
    public function view(SchoolInterface $school)
    {

        $data = array();
        $data['header'] = 'Administration | Import CSV';
        $data['school'] = $school;
        $data['importcsvmodal'] = '
                <ul>
                    <li>Upload all your school\'s administration data via a .csv file.  A .csv file is a "comma separated values" file - a type of Excel file saved in the .csv format.</li>
                    <li>Once the school\'s administration data has been uploaded via the .csv file, an email will be sent to every user advising their logon details to the system, including a randomly generated password (which they will be able to change).</li>
                    <li>Instructions for your .csv file are as follows:
                        <ul>
                            <li>Create a new row in .csv file for every individual Class in the school to be surveyed.</li>
                            <li>All information in the .csv file should be organised into the following columns, in the following order:
                                <ul>
                                    <li>Department;</li>
                                    <li>Class Name;</li>
                                    <li>Class Code;</li>
                                    <li>Year level;</li>
                                    <li>Number of Students;</li>
                                    <li>Teacher Name;</li>
                                    <li>Role Description;</li>
                                    <li>Teacher Email;</li>
                                    <li>Campus Code <em>(optional)</em>.</li>
                                </ul>
                            </li>
                            <li>If a class is only for part of the year, the Class Code should include identifying information as to when the class is administered to students, for example, the semester or term.</li>
                            <li>For "Role Description" there are only two possible options: Teacher or Department Head.</li>
                        </ul>
                    </li>
                </ul>
                A sample is below:
                <table>
                    <tr>
                        <td>Department</td>
                        <td>Class</td>
                        <td>Code</td>
                        <td>Year Level</td>
                        <td>Number of students</td>
                        <td>Teacher</td>
                        <td>Role</td>
                        <td>Email</td>
                        <td>Campus</td>
                    </tr>
                    <tr>
                        <td>History</td>
                        <td>History of Revolutions</td>
                        <td>HIS11AS1</td>
                        <td>11</td>
                        <td>23</td>
                        <td>Jane Day</td>
                        <td>Teacher</td>
                        <td>Jane.Day@exampleschool.edu.au</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>History</td>
                        <td>Australian History</td>
                        <td>HIS9B</td>
                        <td>9</td>
                        <td>20</td>
                        <td>Jane Day</td>
                        <td>Teacher</td>
                        <td>Jane.Day@exampleschool.edu.au</td>
                        <td>campusA</td>
                    </tr>
                    <tr>
                        <td>Science</td>
                        <td>Biology</td>
                        <td>SCI11A</td>
                        <td>11</td>
                        <td>15</td>
                        <td>John Smith</td>
                        <td>Department Head</td>
                        <td>John.Smith@exampleschool.edu.au</td>
                        <td></td>
                    </tr>
                </table>
        ';

        return $this->get_view('importcsv', $data);
    }


    public function process(SchoolInterface $school)
    {
        ini_set('memory_limit','-1');
        ini_set('max_execution_time','-1');

        $row_model = SchoolImport::getModel();
        try {
            $csv = CSV::load(Input::file('csvfile')->getRealPath(), $row_model->setSchool($school));
        } catch (\Illuminate\Filesystem\FileNotFoundException $e) {

            return Redirect::to("/school/view/{$school->id}")->with('message', 'CSV Import Failed File Not Found');
        }

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

        //If we have errors
        if (count($errors) > 0) {
            foreach ($errors as $row_number => $row_errors) {
                $validator_errors[$row_number] = "#" . $row_number . " ";
                foreach ($row_errors as $column_name => $column_errors) {
                    $validator_errors[$row_number] .= "\"col:" . $column_name . "\"";
                    foreach ($column_errors as $column_error) {
                        $validator_errors[$row_number] .= " " . $column_error;
                    }
                }
            }

            return Redirect::to("/school/view/{$school->id}")->withErrors($validator_errors);
        }

        return Redirect::to("/school/view/{$school->id}")->with('message', 'CSV Imported Successfully');

    }

    private function validate_file($school, $reader)
    {
        CSVReader::init_csv_validator($this->required_cols, true);

        $this->school = $school;

        // add rule so that an email is not associated with 2 different name
        Validator::extend('useremailunique', function ($attribute, $reader, $params) {
            $users = $this->school->teachers;
            $usersbyemail = Utils::array_keys_by_field($users, 'email', true);
            $reader->rewind();
            while ($row = $reader->next_row()) {
                $email = strtolower($row['email']);
                if (isset($usersbyemail[$email])) {
                    if ($usersbyemail[$email]->name != $row['teacher']) {
                        return false;
                    }
                } else if (User::where('email','=',$row['email'])->first()) {    // email collide with another school
                    return false;
                }
            }
            return true;
        }, 'ERROR: CSV contains an email address associated with more than one user');

        Validator::extend('validrole', function ($attribute, $reader, $params) {
            $roles = array(User::SCHOOL_ADMIN, User::CAMPUS_LEADER, User::DEPARTMENT_HEAD, User::TEACHER);
            if (Auth::user()->administrator) {
                // only admins can make other admins
                $roles[] = User::PIVOT_ADMIN;
            }
            $reader->rewind();
            while ($row = $reader->next_row()) {
                $role = str_replace(' ', '_', strtolower($row['role']));
                if (!in_array($role, $roles)) {
                    return false;
                }
            }
            return true;
        }, 'ERROR: CSV contains an invalid role name');

        return Validator::make(
            array('csvfile' => $reader),
            array('csvfile' => 'required|requiredcols|samerowlength|useremailunique|validrole')
        );
    }
}