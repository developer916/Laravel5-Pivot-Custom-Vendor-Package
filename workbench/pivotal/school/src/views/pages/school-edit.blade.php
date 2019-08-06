@extends('layout-basic')

@section('content')
{{ Form::model($school, array('route' => ['school.save', $school->id], 'method' => 'POST', 'files' => true)) }}
    <fieldset>
    <legend>{{ $school->id ? 'Edit' : 'Add new' }} school</legend>

    <div class="form-group{{ $errors->has('logo') ? ' has-error' : '' }}">
        {{ Form::label('logo', 'Logo') }}
        {{ Form::file('logo'); }}
        {{ $errors->first('logo', '<p class="help-block">:message</p>') }}
        @if ($school->id && !$errors->has('logo'))
            <p class="help-block">Leave blank to keep existing logo</p>
        @endif
    </div>

    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        {{ Form::label('name', 'Name') }}
        {{ Form::text('name', null, ['class' => 'form-control']) }}
        {{ $errors->first('name', '<p class="help-block">:message</p>') }}
    </div>

    <div class="form-group{{ $errors->has('abbr') ? ' has-error' : '' }}">
        {{ Form::label('abbr', 'Abbr') }}
        {{ Form::text('abbr', null, ['class' => 'form-control']) }}
        {{ $errors->first('abbr', '<p class="help-block">:message</p>') }}
    </div>

    <div class="form-group{{ $errors->has('is_campused') ? ' has-error' : '' }}">
        {{ Form::label('is_campused', 'Has campuses?') }}
        {{ Form::select('is_campused', array('No', 'Yes'), null, ['class' => 'form-control']) }}
        {{ $errors->first('is_campused', '<p class="help-block">:message</p>') }}
    </div>

    {{ Form::submit('Save', ['class'=>'btn btn-pivot']); }}
    @if ($school->id)
        {{ link_to("/school/view/{$school->id}", 'Cancel', array('class'=>'btn btn-link')); }}
    @else
        {{ link_to("/schools", 'Cancel', array('class'=>'btn btn-link')); }}
    @endif
</fieldset>
{{ Form::close() }}

@stop