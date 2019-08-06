<?php

/**
 * Utilities functions
 *
 * @package
 * @subpackage
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

class Utils {

    public static $standards = array(
            'Know students and how they learn',
            'Know the content and how to teach it',
            'Plan for and implement effective teaching and learning',
            'Create and maintain safe and supportive learning environments',
            'Assess, provide feedback and report on student learning'
    );

    // minimum number of disticnt teachers per year/department
    public static $teacher_threshold = 3;

    // minimum number of responses
    public static $responses_threshold = 5;

    //minimum number of department classes with responses
    public static $department_teacher_threshold = 3;

    //minimum number of department classes with responses
    public static $year_teacher_threshold = 3;


    /**
     * Compute the quintile of a value in an array
     * @param number $value the value within the array of values
     * @param array $arrayofvalues the array of values
     * @return int the quintile (from 1 to 5)
     */
    public static function compute_quintile($value, $arrayofvalues, $segmentnum=5) {
        $values = array_values($arrayofvalues);
        sort($values, SORT_NUMERIC);
        $num = count($values);
        for ($i=1; $i<$segmentnum; $i++) {
            $index = (int)round($i*($num/$segmentnum));
            if ($index < $num && $values[$index]>$value) {
                return $i;
            }
        }

        // value is greater than/equal to the highest value
        return $segmentnum;
    }

    public static function weighted_average_array($array) {
        $sum = 0;
        foreach ($array as $score => $count) {
            $sum += $score*$count;
        }

        $arraysum = array_sum($array);

        if ($arraysum == 0) {
            return 0;
        }

        return $sum/$arraysum;
    }

    /**
     * Calculate the sum of 2 arrays by keys, that means values have common keys are added together
     * @param array $array1 the first array
     * @param array $array2 the second array
     * @return array array(key => value). If key exists in array1 and array2, value is the sum of the corresponding 2 values of both
     * Otherwise, take the value in either array
     */
    public static function sum_array($array1, $array2) {
        if (!$array1) {
            $array1 = array();
        }
        if (!$array2) {
            $array2 = array();
        }

        $diff = array_diff_key($array2, $array1);
        $array1 += array_fill_keys(array_keys($diff), 0);
        foreach ($array1 as $index => $value) {
            $array1[$index] = $value + (isset($array2[$index]) ? $array2[$index] : 0);
        }
        return $array1;
    }

    /**
     * Transform an array using a field
     * @param array|collection $array the input array, each element is an object
     * @param string $field the field name of each object
     * @param boolean $lowercase transform the key to lower case (for case insensitive lookup)
     * @return array an array with key is the specified field of each object
     */
    public static function array_keys_by_field($array, $field, $lowercase=false) {
        $results = array();
        foreach ($array as $obj) {
            $results[$lowercase ? strtolower($obj->$field) : $obj->$field] = $obj;
        }
        return $results;
    }

    public static function random_password($length) {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}