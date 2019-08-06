<?php

/**
 * Controller for the help page (the last item on top bar)
 *
 * @package
 * @subpackage
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     tri.le
 * @version    1.0
 */

class HelpController extends BaseController {

    function view() {
        $data = array();
        $data['header'] = 'Help Topics and FAQs';
        return $this->get_view('help', $data);
    }

}