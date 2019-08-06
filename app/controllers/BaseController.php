<?php

class BaseController extends Controller {

    private $jscalls = array();
    private $jsfiles = array();

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if ( ! is_null($this->layout))
        {
            $this->layout = View::make($this->layout);
        }
    }

    protected function js_include($file) {
        $this->jsfiles[] = $file;
    }

    protected function js_call($functioname, $params, $jsfile) {
        $this->jscalls[]= array('function' => $functioname, 'params' => $params);
        $this->jsfiles[] = $jsfile;
    }

    protected function get_view($viewname, $data) {
        $jsfiles = '';
        foreach ($this->jsfiles as $file) {
            $jsfiles .= HTML::script($file.'?v=1');
        }

        $jscalls = '';
        foreach ($this->jscalls as $call) {
            $jscalls .= $call['function'].'('.json_encode($call['params']).');';
        }
        $data['jsfiles'] = $jsfiles;
        $data['jscalls'] = $jscalls;
        return View::make($viewname, $data);
    }

    public function invoke_tooltip () {
        $this->js_call('draw_tooltips', null, '/javascript/tooltip.js');
    }
}
