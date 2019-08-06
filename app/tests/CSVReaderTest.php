<?php

/**
 * Test class for CSV reader
 *
 * @package    
 * @subpackage 
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

class CSVReaderTest extends TestCase {

    public function test_import_csv() {
        $filepath = __DIR__.'/data/test.csv';
        $reader = new CSVReader($filepath);

        $columnnames = $reader->columns();
        $this->assertEquals($columnnames, array('department', 'class', 'code', 'year level', 'number of students', 'teacher', 'email'));

        $nullrow = $reader->current_row();
        $this->assertNull($nullrow);

        $firstrow = $reader->next_row();
        $this->assertEquals(array_keys($firstrow), $columnnames);
        $this->assertEquals('History', $firstrow['department']);
        $this->assertEquals('History of Revolutions', $firstrow['class']);
        $this->assertEquals('HIST1A', $firstrow['code']);
        $this->assertEquals('12', $firstrow['year level']);
        $this->assertEquals('23', $firstrow['number of students']);
        $this->assertEquals('Karl Marx', $firstrow['teacher']);
        $this->assertEquals('karl.marx@exampleschool.edu.au', $firstrow['email']);

        $rownum = 1;
        while ($row = $reader->next_row()) {
            $rownum++;
        }
        $this->assertEquals(5, $rownum);
    }

}