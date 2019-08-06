@extends('layout-basic')

@section('content')
{{ Form::model($class, array('route' => ['class.save', $school->id, $class->id])) }}
    <fieldset>
    <legend>{{ $class->id ? 'Edit' : 'Add new' }} class</legend>

    <div class="form-group{{ $errors->has('department_id') ? ' has-error' : '' }}">
        {{ Form::label('department_id', 'Department') }}
        {{ Form::select('department_id', $departments, null, ['class' => 'form-control']) }}
        {{ $errors->first('department_id', '<p class="help-block">:message</p>') }}
    </div>

    <div class="form-group{{ $errors->has('teacher_id') ? ' has-error' : '' }}">
        {{ Form::label('teacher_id', 'Teacher') }}
        {{ Form::select('teacher_id', $teachers, null, ['class' => 'form-control']) }}
        {{ $errors->first('teacher_id', '<p class="help-block">:message</p>') }}
    </div>

    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        {{ Form::label('name', 'Name') }}
        {{ Form::text('name', null, ['class' => 'form-control']) }}
        {{ $errors->first('name', '<p class="help-block">:message</p>') }}
    </div>

    <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
        {{ Form::label('code', 'Code') }}
        {{ Form::text('code', null, ['class' => 'form-control']) }}
        {{ $errors->first('code', '<p class="help-block">:message</p>') }}
    </div>

    <div class="form-group{{ $errors->has('year_level') ? ' has-error' : '' }}">
        {{ Form::label('year_level', 'Year level') }}
        {{ Form::select('year_level', $yearlevels, null, ['class' => 'form-control']) }}
        {{ $errors->first('year_level', '<p class="help-block">:message</p>') }}
    </div>

    <div class="form-group{{ $errors->has('num_students') ? ' has-error' : '' }}">
        {{ Form::label('num_students', 'Number of students') }}
        {{ Form::text('num_students', null, ['class' => 'form-control']) }}
        {{ $errors->first('num_students', '<p class="help-block">:message</p>') }}
    </div>

    {{ Form::submit('Save', ['class'=>'btn btn-pivot']); }}
    {{ link_to("/school/view/{$school->id}", 'Cancel', array('class'=>'btn btn-link')); }}
</fieldset>
{{ Form::close() }}

@stop