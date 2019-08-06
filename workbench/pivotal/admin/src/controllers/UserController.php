<?php namespace Pivotal\Admin\Controllers;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use \Pivotal\Csv\Facades\Csv;
use Pivotal\User\Models\Import\MetaImport;
use Pivotal\School\Models\School;
use Symfony\Component\Debug\Exception\FatalErrorException;

class UserController extends Controller
{

    public function importMeta($school_id = null)
    {

        $data = [
            'school' => School::where('id','=',$school_id)->firstOrFail(),
            'header' => 'Import User Meta CSV'
        ];
        return \View::make('admin::pages.user.import.meta', $data);
    }


    public function importMetaPost($school_id = null)
    {
        $school = School::where('id','=',$school_id)->firstOrFail();

        $row_model = MetaImport::getModel();


        try {
            $csv = Csv::load(Input::file('csvfile')->getRealPath(), $row_model->setSchool($school));
        } catch (\Illuminate\Filesystem\FileNotFoundException $e) {

            return Redirect::route('admin.user.import.meta')->with('message', 'CSV Import Failed File Not Found');
        } catch(\Symfony\Component\Debug\Exception\FatalErrorException $e)
        {
            return Redirect::route('admin.user.import.meta')->with('message', 'No File Found');
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

            return \Redirect::route('admin.user.import.meta',$school_id)->withErrors($validator_errors);
        }

        return \Redirect::route('admin.user.import.meta',$school_id)->with('message', 'CSV Imported Successfully');

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