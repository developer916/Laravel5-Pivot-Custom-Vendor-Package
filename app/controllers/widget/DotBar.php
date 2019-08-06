<?php

/**
 * Quartile bar display
 *
 * @package    
 * @subpackage 
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

require_once(__DIR__.'/Widget.php');

class DotBar extends Widget {

    private $value;
    private $numval;

    private $selectedcolor = '#F5A04A';
    private $normalcolor = '#FEEDDA';

    protected $class = 'dotbar';

    public function __construct($value, $numval=5, $height=50, $width='auto') {
        $this->value = $value;
        $this->numval = $numval;
        if ($width=='auto') {
            $width = $height*$numval;
        }
        parent::__construct($width, $height, 'transparent');
    }

    protected function output_widget() {
        $html = '';
        $unitwidth = $this->width/$this->numval;
        for ($i=0; $i<$this->numval; $i++) {
            $position = $i*$unitwidth;
            $styleouter = "top:0;left:{$position}px;width:{$unitwidth}px;height:{$this->height}px";
            $styleinner = "background-color:".($i+1==$this->value ? $this->selectedcolor : $this->normalcolor);
            $html .= '<div class="item" style="'.$styleouter.'"><div class="dot" style="'.$styleinner.'"></div></div>';
        }
        return $html;
    }
}