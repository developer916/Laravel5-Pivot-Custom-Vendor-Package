@extends('layout-basic')

@section('content')
{{ Form::model($department, array('route' => ['department.save', $school->id, $department->id])) }}
    <fieldset>
    <legend>{{ $department->id ? 'Edit' : 'Add new' }} department</legend>

    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        {{ Form::label('name', 'Name') }}
        {{ Form::text('name', null, ['class' => 'form-control']) }}
        {{ $errors->first('name', '<p class="help-block">:message</p>') }}
    </div>

    {{ Form::submit('Save', ['class'=>'btn btn-pivot']); }}
    {{ link_to("/school/view/{$school->id}", 'Cancel', array('class'=>'btn btn-link')); }}
</fieldset>
{{ Form::close() }}

@stop