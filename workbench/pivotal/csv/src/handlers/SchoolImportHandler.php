<?php namespace Pivotal\Csv\Handlers;

use Illuminate\Support\Facades\Validator;
use Pivotal\Csv\Models\SchoolImport;

class SchoolImportHandler
{

    /**
     * Provides the rules for validation
     * @return array
     */
    private function getRules()
    {
        return array(
            'department' => 'required|regex:/^[\w -=@]+$/',
            'class' => 'required|regex:/^[\w -=@]+$/',
            'code' => 'required|regex:/^[\w -=@]+$/',
            'year level' => 'required|numeric|min:3|max:12',
            'number of students' => 'required|numeric',
            'teacher' => 'required',
            'role' => 'required|regex:/^[a-z\d\-_\s]+$/i|in:Teacher,Department Head,Department head',
            'email' => 'required|email'
        );
    }


    /**
     * Validate the column values generically
     */
    public function handle(SchoolImport $schoolImport)
    {
        $row = $schoolImport->getAttributes();

        $row['department'] = trim($row['department']);
        $row['class'] = trim($row['class']);
        $row['code'] = trim($row['code']);
        $row['year level'] = trim($row['year level']);
        $row['number of students'] = trim($row['number of students']);
        $row['teacher'] = trim($row['teacher']);
        $row['role'] = trim($row['role']);
        $row['email'] = trim($row['email']);

        $validator = Validator::make($row, $this->getRules());

        if ($validator->fails()) {
            $schoolImport
                ->setInvalid()
                ->setErrors($validator->messages()->getMessages());
        } else {
            $schoolImport
                ->setValid()
                ->setErrors();
        }

        $schoolImport->role = str_replace(' ', '_', strtolower($schoolImport->role));

        foreach ($row as $k => $t) {
            $schoolImport->setAttribute($k, $t);
        }

        return $schoolImport;
    }

}