<?php

/**
 * Test the Utils class
 *
 * @package    
 * @subpackage 
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

require_once('../controllers/classes/Utils.php');
class UtilsTest extends TestCase {

    public function test_compute_quintile() {
        $arrayofvalues1 = array(1, 1, 1, 2, 2, 2, 3, 3, 3);
        $this->assertEquals(1, Utils::compute_quintile(1, $arrayofvalues1, 3));
        $this->assertEquals(2, Utils::compute_quintile(2, $arrayofvalues1, 3));
        $this->assertEquals(3, Utils::compute_quintile(3, $arrayofvalues1, 3));

        $arrayofvalues2 = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
        $this->assertEquals(1, Utils::compute_quintile(0, $arrayofvalues2, 3));
        $this->assertEquals(1, Utils::compute_quintile(2, $arrayofvalues2, 3));
        $this->assertEquals(1, Utils::compute_quintile(3, $arrayofvalues2, 3));
        $this->assertEquals(2, Utils::compute_quintile(4, $arrayofvalues2, 3));
        $this->assertEquals(2, Utils::compute_quintile(6, $arrayofvalues2, 3));
        $this->assertEquals(3, Utils::compute_quintile(7, $arrayofvalues2, 3));
        $this->assertEquals(3, Utils::compute_quintile(9, $arrayofvalues2, 3));
        $this->assertEquals(3, Utils::compute_quintile(10, $arrayofvalues2, 3));

        $arrayofvalues3 = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);
        $this->assertEquals(1, Utils::compute_quintile(0, $arrayofvalues3, 3));
        $this->assertEquals(1, Utils::compute_quintile(2, $arrayofvalues3, 3));
        $this->assertEquals(1, Utils::compute_quintile(3, $arrayofvalues3, 3));
        $this->assertEquals(1, Utils::compute_quintile(4, $arrayofvalues3, 3));
        $this->assertEquals(2, Utils::compute_quintile(5, $arrayofvalues3, 3));
        $this->assertEquals(2, Utils::compute_quintile(7, $arrayofvalues3, 3));
        $this->assertEquals(3, Utils::compute_quintile(8, $arrayofvalues3, 3));
        $this->assertEquals(3, Utils::compute_quintile(9, $arrayofvalues3, 3));
        $this->assertEquals(3, Utils::compute_quintile(10, $arrayofvalues3, 3));
    }

    public function test_weighted_average_array() {
        $value1 = Utils::weighted_average_array(array(1 => 3, 2 => 3, 3 => 3));
        $this->assertEquals(2, $value1);

        $value2 = Utils::weighted_average_array(array(1 => 10, 2 => 5));
        $this->assertEquals(4/3, $value2);
    }

    public function test_sum_array_normal() {
        $array1 = array(1, 2, 3);
        $array2 = array(4, 5, 6);
        $array = Utils::sum_array($array1, $array2);
        $this->assertEquals($array, array(5, 7, 9));
    }

    public function test_sum_array_superfluous() {
        $array1 = array('a' => 1, 'b' => 2, 'c' => 3);
        $array2 = array('a' => 5, 'b' => 4, 'e' => 3);

        // left
        $arrayresult1 = Utils::sum_array($array1, $array2);
        $this->assertEquals($arrayresult1, array('a' => 6, 'b' => 6, 'c' => 3, 'e' => 3));

        // right
        $arrayresult2 = Utils::sum_array($array2, $array1);
        $this->assertEquals($arrayresult2, array('a' => 6, 'b' => 6, 'c' => 3, 'e' => 3));

        // null left
        $arrayresult3 = Utils::sum_array(null, $array1);
        $this->assertEquals($arrayresult3, $array1);

        // null left
        $arrayresult4 = Utils::sum_array($array2, null);
        $this->assertEquals($arrayresult4, $array2);
    }

    public function test_array_keys_by_field() {
        $array = array(
            (object)array('name' => 'John Phoon', 'email' => 'John.Phoon@ninelanterns.com.au'),
            (object)array('name' => 'Tri Le', 'email' => 'Tri.Le@ninelanterns.com.au'),
            (object)array('name' => 'James Ballard', 'email' => 'James.Ballard@ninelanterns.com.au'),
            (object)array('name' => 'Russell Grocott', 'email' => 'Russell.Grocott@ninelanterns.com.au'),
        );
        $result1 = Utils::array_keys_by_field($array, 'email');
        $this->assertEquals('James Ballard', $result1['James.Ballard@ninelanterns.com.au']->name);
        $this->assertEquals('John Phoon', $result1['John.Phoon@ninelanterns.com.au']->name);

        $result2 = Utils::array_keys_by_field($array, 'email', true);
        $this->assertEquals('James Ballard', $result2['james.ballard@ninelanterns.com.au']->name);
        $this->assertEquals('John Phoon', $result2['john.phoon@ninelanterns.com.au']->name);
    }
}
