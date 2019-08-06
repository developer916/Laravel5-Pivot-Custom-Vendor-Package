<?php namespace Pivotal\Csv;

interface CsvInterface
{
    public function getFilePath();

    public function setFilePath($file_path = null);

    public function getColumnNames();

    public function setColumnNames($column_names = array());

    public function getColumnName($id);

    public function getReader();

    public function setReader(ReaderInterface $reader);

    public function load();
}