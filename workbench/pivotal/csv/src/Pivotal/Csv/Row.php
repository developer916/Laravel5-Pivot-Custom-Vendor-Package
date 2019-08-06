<?php namespace Pivotal\Csv;

use Illuminate\Database\Eloquent\Model;

class Row extends Model implements RowInterface
{

    /**
     * Returns a clone of the current model instance with its attributes cleared
     * @return Row
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

}