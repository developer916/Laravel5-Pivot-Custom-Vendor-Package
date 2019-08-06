<?php namespace Pivotal\Csv;

/**
 * CSV Reader class
 *
 * @copyright  &copy; 2015 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

class Reader implements ReaderInterface {

    private $columnnames = null;
    private $handle = null;
    private $currentrow = null;
    private $filepath = null;
    private $csv;
    const DELIMITER = ',';

    public function __construct()
    {
        ini_set('auto_detect_line_endings',true); // handle stupid OSX line endings
    }

    public function load(CsvInterface $csv)
    {
        $this->csv = $csv;
        $this->filepath = $csv->getFilePath();

        if (!file_exists($this->filepath)) {
            throw new \Illuminate\Filesystem\FileNotFoundException('Cannot find file at ' . $this->filepath);
        }

        $this->open_file();
    }


    public function getCsv()
    {
        return $this->csv;
    }

    private function open_file() {


        ini_set('auto_detect_line_endings',true);

        if (($this->handle = fopen($this->filepath, 'r'))===false) {
            throw new Exception("Cannot open file $this->filepath");
        }

        $this->getCsv()->setColumnNames(fgetcsv($this->handle));

        $row = 1;

        //Set the rows to the csv object
        if (($handle = fopen($this->filepath, "r")) !== FALSE) {

            $set_rows = [];

            while (($data = fgetcsv($handle, 1000, self::DELIMITER)) !== FALSE) {
                $num = count($data);
                $row++;
                for ($c = 0; $c < $num; $c++) {
                    $set_rows[$row - 1][$this->getCsv()->getColumnName($c)] = $data[$c];
                }
            }

            $this->getCsv()->setRows($set_rows);

            fclose($handle);
        }
    }
}