<?php

/**
 * The chart base class
 *
 * @package    
 * @subpackage 
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

abstract class Widget {

    protected $width;
    protected $height;
    protected $background;
    protected $class;

    public function __construct($width, $height, $background) {
        $this->width = $width;
        $this->height = $height;
        $this->background = $background;
    }

    protected abstract function output_widget();

    public function output() {
        return '<div class="widgetdiv '.$this->class.'" style="width:'.$this->width.'px;height:'.$this->height.'px;background-color:'.$this->background.'">'.$this->output_widget().'</div>';
    }
}