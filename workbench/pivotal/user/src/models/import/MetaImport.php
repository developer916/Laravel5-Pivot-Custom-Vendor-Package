<?php namespace Pivotal\User\Models\Import;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Pivotal\Campus\Models\Campus;
use Pivotal\Course\Models\Course;
use Pivotal\Csv\Row;
use Pivotal\Csv\RowInterface;
use Pivotal\School\Models\SchoolInterface;
use Pivotal\User\Models\User;

class MetaImport extends Model implements RowInterface
{

    private $errors = array();
    private $school;
    private $user;
    private $valid = false;
    private $headerRow;


    /**
     * Returns a clone of the current model instance with its attributes cleared
     * @return SchoolImport
     */
    public function getInstance()
    {
        $new_instance = clone $this;
        $new_instance->fill([]);

        return $new_instance;
    }

    /**
     * Assign the header row to this row
     * @param RowInterface $row
     */
    public function setHeaderRow(RowInterface $row)
    {
        $this->headerRow = $row;
    }



    /**
     * Validate that the required fields have correct values and add entities where appropriate
     * @param array $options
     * @return $this
     */
    public function save(array $options = array())
    {
        Event::fire('user.import.meta.save.before', array($this));

        $user = User::where('email','=',$this->email)->first();

        if(isset($user->id))
        {
            $meta_data = $this->toArray();
            unset($meta_data['email']);
             $user->meta = $meta_data;
        }
        return $this;
    }

    /**
     * @param SchoolInterface $school
     * @return $this
     */
    public function setSchool(SchoolInterface $school)
    {
        $this->school = $school;
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return isset($this->email);
    }


    /**
     * @return $this
     */
    public function setInvalid()
    {
        $this->valid = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function setValid()
    {
        $this->valid = true;
        return $this;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors = array())
    {
        $this->errors = $errors;
    }


    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}