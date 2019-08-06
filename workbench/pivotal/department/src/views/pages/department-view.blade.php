@extends('layout-basic')

@section('content')
@if(Session::get('error'))
  <div class="alert alert-dismissable alert-danger">
    <button data-dismiss="alert" class="close" type="button">×</button>
    {{ Session::get('error') }}
  </div>
@endif
@if(Session::get('message'))
  <div class="alert alert-dismissable alert-success">
    <button data-dismiss="alert" class="close" type="button">×</button>
    {{ Session::get('message') }}
  </div>
@endif
<div class="row show-grid">
    <div class="col-md-2">
        <img title="{{ $department->school->name }}" class="img-responsive img-rounded" src="{{ $department->school->logo->url('medium'); }}" />
    </div>
    <div class="col-md-4">
        <table class="table table-condensed table-pivot">
            <tr>
                <th>School</th>
                <td>{{ $department->school->name }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $department->name }}</td>
            </tr>
            <tr>
                <th>Department Head</th>
                <td>@if ($department->heads->count())
                        @foreach($department->heads as $head)
                            {{ $head->name }} <br />
                        @endforeach
                    @endif
                </td>
            </tr>
        </table>

    </div>
    @if (Auth::user()->role == User::TEACHER || Auth::user()->role == User::DEPARTMENT_HEAD )
        <div class="col-md-6 welcome_message">
            @include('welcome-message')
        </div>
    @endif
</div>

@if (Auth::user()->role == User::DEPARTMENT_HEAD)
<div class="row show-grid">
    <div class="col-md-12">
        {{ $cycle_panel }}
    </div>
</div>
@endif
<div class="row show-grid">
    <div class="col-md-4">
        {{ $classes_panel }}
    </div>
    <div class="col-md-4">
        {{ $teachers_panel }}
    </div>
</div>
@if(\Auth::user()->proxies->count() > 0)
    <div class="row show-grid">
        <div class="col-xs-5">
            <div class="panel panel-pivot">
                <div class="col-xs-7 panel-heading">
                    Alternate Accounts
                </div>
            </div>
            <div class="panel-body col-xs-12">
                @foreach(Auth::user()->proxies as $proxy)
                    <div class="col-xs-12"><a href="{{URL::route('user.login.as',$proxy->id)}}">{{$proxy->name}}</a></div>
                @endforeach
            </div>
        </div>
    </div>
@endif
@if (Auth::user()->role == User::DEPARTMENT_HEAD || Auth::user()->role == \User::TEACHER && 1 == 2  )
<div class="row show-grid">
    <div class="col-md-4">
        <div class="panel panel-pivot borderless" style="margin-bottom: 0">
            <div class="col-xs-8 panel-heading" style="float: left !important; margin: 0; padding: 10px 5px">
                Teacher Self Assessment
            </div>
            <div class="col-xs-4" style="margin: 0; padding: 0;">
                <div class="btn-status panel-heading">
                    {{ ucfirst(Auth::user()->getAssessmentStatus()) }}
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            Take this optional survey to see how your perceptions of your strengths compare with your students' views. Your 'self assessment' results will appear on your Question Breakdown report.

            @if(!Auth::user()->isSelfAssessmentComplete())
                <div style="margin-top:10px;">
                    <a target="_blank" href="/reports/self_assessment">
                        <div class="btn btn-pivot btn">
                            Do the survey now
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-pivot borderless" style="margin-bottom: 0">
            <div class="col-xs-12 panel-heading">
                Survey Preview
            </div>
        </div>
        <div class="panel-body">

            You can view the survey questions that your students are asked to answer here. Your results will not be recorded.

            <div style="margin-top:10px;" class="col-xs-12">
                <a target="_blank" href="{{ Config::get('limesurvey.surveyurl'); }}survey/index/sid/{{ Config::get('limesurvey.template'); }}/newtest/Y/lang/en">
                    <div class="btn btn-pivot btn">
                        Preview the survey here
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@if (Auth::user()->isEditor())
    <p>
        {{ link_to("/department/edit/{$department->school->id}/{$department->id}", 'Edit department', array('class'=>'btn btn-pivot')); }}
    </p>
@endif
@stop



