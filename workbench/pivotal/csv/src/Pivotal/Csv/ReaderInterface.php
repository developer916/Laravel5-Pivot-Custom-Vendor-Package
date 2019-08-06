<?php namespace Pivotal\Csv;

interface ReaderInterface
{
    public function load(CsvInterface $csv);
}