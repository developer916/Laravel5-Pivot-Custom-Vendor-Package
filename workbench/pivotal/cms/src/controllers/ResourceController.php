<?php namespace Pivotal\Cms\Controllers;

class ResourceController extends BaseCmsController
{
    public function index()
    {
        $data = array();
        $data['header'] = 'Resources';
        return $this->get_view('cms::pages.resource.index', $data);
    }

    public function byQuestion()
    {
        $data = array();
        $data['header'] = 'Question Resources';
        return $this->get_view('cms::pages.resource.byquestion', $data);
    }


    public function general()
    {
        $data = array();
        $data['header'] = 'General Resources';
        return $this->get_view('cms::pages.resource.general', $data);
    }
}