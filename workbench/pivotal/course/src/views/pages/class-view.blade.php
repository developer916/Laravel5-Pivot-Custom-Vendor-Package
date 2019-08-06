@extends('layout-basic')

@section('content')
@if(Session::get('message'))
  <div class="alert alert-dismissable alert-success">
    <button data-dismiss="alert" class="close" type="button">×</button>
    {{ Session::get('message') }}
  </div>
@endif
<div class="row show-grid">
    <div class="col-md-2">
        <img class="img-responsive img-rounded school-logo" title="{{ $class->department->school->name }}" src="{{ $class->department->school->logo->url('medium'); }}" />
    </div>
    <div class="col-md-4">
        <table class="table table-condensed table-pivot">
            <tr>
                <th>Teacher</th>
                <td>
                    @if (Pivotal\User\Controllers\UserController::can_access($class->teacher))
                        {{ link_to("/user/view/{$class->teacher->id}", $class->teacher->name); }}
                    @else
                        {{ $class->teacher->name }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Department</th>
                <td>
                    @if (Pivotal\Department\Controllers\DepartmentController::can_access($department))
                        {{ link_to("/department/view/$department->id", $department->name); }}
                    @else
                        {{ $department->name }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $class->name }}</td>
            </tr>
            <tr>
                <th>Code</th>
                <td>{{ $class->code }}</td>
            </tr>
            <tr>
                <th>Year level</th>
                <td>{{ $class->year_level }}</td>
            </tr>
            <tr>
                <th>Number of students</th>
                <td>{{ $class->num_students }}</td>
            </tr>
        </table>
    </div>
</div>
<div class="row show-grid">
    <div class="col-md-12">
        {{ $classescycles_panel }}
    </div>
</div>
@if (Auth::user()->isEditor())
    <p>
        {{ link_to("/class/edit/{$school->id}/{$department->id}/{$class->id}", 'Edit class', array('class'=>'btn btn-pivot')); }}
    </p>
@endif
@stop