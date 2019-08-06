<?php namespace Pivotal\Csv;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Csv implements CsvInterface
{
    private $reader;
    private $filePath;
    private $columnNames;
    private $headerRow;
    private $rowModel;
    private $rowCollection;
    private $rows = array();

    /**
     * Load the csv into the reader
     * @return $this
     */
    public function load()
    {
        $this->getReader()->load($this);
        return $this;
    }

    /**
     * @return ReaderInterface
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * @param ReaderInterface $reader
     * @return $this
     */
    public function setReader(ReaderInterface $reader)
    {
        $this->reader = $reader;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $file_path
     * @return $this
     */
    public function setFilePath($file_path = null)
    {
        $this->filePath = $file_path;
        return $this;
    }

    /**
     * @return array()
     */
    public function getColumnNames()
    {
        return $this->columnNames;
    }

    /**
     * @param array $column_names
     * @return $this
     */
    public function setColumnNames($column_names = array())
    {
        if (is_array($column_names)) {
            foreach ($column_names as $index => $value) {
                $this->columnNames[$index] = strtolower(trim($value));
            }
        }
        return $this;
    }

    /**
     * Get Column Name by Id based on position in the first row
     * @param $id
     * @return null
     */
    public function getColumnName($id)
    {
        if (isset($this->columnNames[$id])) {
            return $this->columnNames[$id];
        }
        return null;
    }

    /**
     * @return RowInterface
     */
    public function getRowModel()
    {
        if (is_object($this->rowModel)) {
            return $this->rowModel;
        }
        return new Row();
    }

    /**
     * @param RowInterface $model
     * @return $this
     */
    public function setRowModel(RowInterface $model)
    {
        $this->rowModel = $model;
        return $this;
    }

    /**
     * @return RowCollectionInterface
     */
    public function getRowCollection()
    {
        if (is_object($this->rowCollection)) {
            return $this->rowCollection;
        }
        return new RowCollection;
    }

    /**
     * @param RowCollectionInterface $collection
     * @return $this
     */
    public function setRowCollection(RowCollectionInterface $collection)
    {
        $this->rowCollection = $collection;
        return $this;
    }

    /**
     * @return RowInterface
     */
    public function getHeaderRow()
    {
        return $this->headerRow;
    }

    /**
     * @param RowInterface $row
     */
    public function setHeaderRow(RowInterface $row)
    {
        $this->headerRow = $row;
        return $this;
    }


    /**
     * Set the rows based on an array of keyed key value pairs
     * @param array $rows
     * @return $this
     */
    public function setRows($rows = array())
    {
        $this->rows = $this->getRowCollection();
        $i = 0;

        foreach ($rows as $row) {

            $new_row = $this->getRowModel()->getInstance();
            if ($i == 0) {
                $this->setHeaderRow($new_row);
            } else {
                //Identify header row
                $new_row->setHeaderRow($this->getHeaderRow());

                foreach ($row as $k => $v) {
                    if (!$k) {
                        continue;
                    }
                    $new_row->{$k} = $v;
                }
                $this->rows->add($new_row);
            }
            $i++;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

}