<?php

/**
 * Horizontal bar for charting
 *
 * @package
 * @subpackage
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

require_once(__DIR__.'/Widget.php');

class HorizontalBar extends Widget {

    private $value;
    private $maxvalue;
    private $scale;

    private $foreground = '#72699B';
    private $linecolor = '#9E97BB';

    protected $class = 'horizontalbar';

    public function __construct($value, $maxvalue=5, $scale=1, $width='auto', $height=50) {
        $this->value = $value;
        $this->maxvalue = $maxvalue;
        $this->scale = $scale;
        if ($width=='auto') {
            $width = $height*($maxvalue/$scale);
        }
        parent::__construct($width, $height, '#D7D4E3');
    }

    protected function output_widget() {
        $unitwidth = $this->width/$this->maxvalue;
        $filledsize = $this->value*$unitwidth;
        $filleddiv = '<div class="filled item" style="width:'.$filledsize.'px;height:'.$this->height.'px;top:0;left:0;background-color:'.$this->foreground.'"></div>';

        // draw the grid
        $divs = '';
        $unitnum = $this->maxvalue/$this->scale;
        for ($i=0; $i<$unitnum; $i++) {
            $leftpos = $i*$unitwidth;
            $style = "width:{$unitwidth}px;height:{$this->height}px;top:0;left:{$leftpos}px;";
            if ($i>0) {
                $style .= "border-left:1px solid $this->linecolor";
            }
            $divs .= '<div class="grid item" style="'.$style.'"></div>';
        }

        $filleddiv1 = '<div class="filled item" style="width:'.$filledsize.'px;height:'.$this->height.'px;top:0;left:0;background-color:transparent;line-height:'.$this->height.'px">'.
                    number_format($this->value, 1).
                    '</div>';

        return $filleddiv.$divs.$filleddiv1;
    }
}