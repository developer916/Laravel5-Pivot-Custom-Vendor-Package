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
    <div class="col-md-1">
        <img class="img-responsive img-rounded school-logo" title="{{ $school->name }}" src="{{ $school->logo->url('medium'); }}" />
    </div>
    <div class="col-md-5">
        <table class="table table-condensed table-pivot teacher-info">
            <tr>
                <th>School</th>
                <td>{{ $school->name }}</td>
            </tr>
            @if ($user->department)
                <tr>
                    <th>Department</th>
                    <td>
                        @if (Pivotal\Department\Controllers\DepartmentController::can_access($user->department))
                            {{ link_to("/department/view/{$user->department->id}", $user->department->name); }}
                        @else
                            {{ $user->department->name }}
                        @endif
                    </td>
                </tr>
            @endif
            <tr>
                <th>Name</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>Role</th>
                <td>{{ ucfirst(str_replace('_', ' ', $user->role)); }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
            </tr>
            @if(Auth::user()->role == User::PIVOT_ADMIN && $user->role != User::PIVOT_ADMIN && $user->id != Auth::user()->id)
            <tr>
                <th></th>
                <td> {{ link_to("/user/login_as/$user->id", 'Login as'); }}</td>
            </tr>
            @endif
        </table>
    </div>
    @if (Auth::user()->role == User::TEACHER || Auth::user()->role == User::DEPARTMENT_HEAD )
        <div class="col-md-6 welcome_message">
            @include('welcome-message')
        </div>
    @endif
</div><br/>
@if(count(Auth::user()->proxies) > 0)
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
@if ($user->school)
    <div class="row show-grid">
        <div class="col-md-4">
            {{ $departments_panel }}
        </div>
        <div class="col-md-4">
            {{ $classes_panel }}
        </div>
    </div>
@endif

    <div class="row show-grid">
        <div class="col-md-4">
            <div class="panel panel-pivot borderless" style="margin-bottom: 0">
                <div class="col-xs-8 panel-heading" style="float: left !important; margin: 0; padding: 10px 5px">
                    Teacher Self Assessment
                </div>
                <div class="col-xs-4" style="margin: 0; padding: 0;">
                    <div class="btn-status panel-heading">
                        {{ ucfirst($user->getAssessmentStatus()) }}
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-body">
                Take this optional survey to see how your perceptions of your strengths compare with your students' views. Your 'self assessment' results will appear on your Question Breakdown report.

                @if($user->getAssessmentStatus() == \Pivotal\Survey\Models\Assessment::STATUS_INCOMPLETE && \Auth::user()->id == $user->id)
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

<p>
@if (Auth::user()->isAdministrator())
    {{ link_to("/user/edit/{$user->id}", 'Edit user', array('class'=>'btn btn-pivot')); }}
@endif
</p>
@stop