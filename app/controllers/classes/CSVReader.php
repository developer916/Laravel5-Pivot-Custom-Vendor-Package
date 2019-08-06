<?php

/**
 * CSV Reader class
 *
 * @copyright  &copy; 2015 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

class CSVReader {

    private $columnnames = null;
    private $handle = null;
    private $currentrow = null;
    private $filepath = null;

    public function __construct($filepath) {
        if (!file_exists($filepath)) {
            throw new Illuminate\Filesystem\FileNotFoundException('Cannot find file at '.$filepath);
        }
        $this->filepath = $filepath;
        $this->open_file();
    }

    private function open_file() {
        ini_set('auto_detect_line_endings',true); // handle stupid OSX line endings
        if (($this->handle = fopen($this->filepath, 'r'))===false) {
            throw new Exception("Cannot open file $this->filepath");
        }

        $this->columnnames = fgetcsv($this->handle);
        foreach ($this->columnnames as $index => $value) {
            $this->columnnames[$index] = strtolower(trim($value));
        }
    }

    /**
     * Restart again
     */
    public function rewind() {
        $this->close();
        $this->open_file();
    }

    public function current_row() {
        return $this->currentrow;
    }

    public function next_row() {
        $data = fgetcsv($this->handle);
        $row = null;
        if ($data) {
            $row = array();
            foreach ($data as $index => $value) {
                $row[$this->columnnames[$index]] = trim($value);
            }
        }
        $this->currentrow = $row;
        return $row;
    }

    public function columns() {
        return $this->columnnames;
    }

    public function close() {
        if ($this->handle) {
            fclose($this->handle);
            $this->handle = null;
        }
    }

    public function __destruct() {
        $this->close();
    }

    public static function init_csv_validator($requiredcols = null, $samerowlength=true) {
        if ($requiredcols) {
            Validator::extend('requiredcols', function($attribute, $reader, $params) use($requiredcols) {
                $csvcols = $reader->columns();
                $reader->close();
                return count(array_diff($requiredcols, $csvcols))===0;
            }, 'ERROR: CSV must contain the following columns <ul class="help-block"><li>'.implode('</li><li>', $requiredcols).'</li></ul>');
        }

        if ($samerowlength) {
            Validator::extend('samerowlength', function($attribute, $reader, $params) {
                $reader->rewind();
                $colcount = count($reader->columns());
                $valid = true;
                while ($row = $reader->next_row()) {
                    if (count($row)!=$colcount) {
                        $valid = false;
                        break;
                    }
                }
                $reader->close();
                return $valid;
            }, 'ERROR: CSV must have a consistent number of columns in each row');
        }
    }
}