<?php namespace Pivotal\Csv;

class CsvFactory
{
    private $validator;
    private $reader;
    private $csv;


    public function __construct(\Illuminate\Validation\Factory $validator,ReaderInterface $reader)
    {
        ini_set('auto_detect_line_endings',true);
        $this->validator = $validator;
        $this->reader = $reader;
    }

    /**
     * Load the file and attach the reader to the instance
     * @param null $filepath
     */
    public function load($filepath = null, $row_model = null, $row_collection = null)
    {
        if (!file_exists($filepath)) {
            throw new \Illuminate\Filesystem\FileNotFoundException('Cannot find file at '.$filepath);
        }

        $this->csv = new Csv();
        $this->csv
            ->setReader($this->getReader())
            ->setFilePath($filepath);

        if ($row_model) {
            $this->csv->setRowModel($row_model);
        }
        if ($row_collection) {
            $this->csv->setRowCollection($row_collection);
        }

        return $this->csv->load();
    }

    /**
     * @return ReaderInterface
     */
    private function getReader()
    {
        return $this->reader;
    }

    /**
     * @return CsvInterface
     */
    private function getCsv()
    {
        return $this->csv;
    }

}