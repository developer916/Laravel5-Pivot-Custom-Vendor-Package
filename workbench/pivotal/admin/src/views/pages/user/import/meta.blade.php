@extends('layout-basic')

@section('content')
{{ Form::model($school, array('route' => ['admin.user.import.meta.post', $school->id], 'method' => 'POST', 'files' => true)) }}
<fieldset>
    <div class="form-group{{ $errors->has('csvfile') ? ' has-error' : '' }}">
        {{ Form::label('csvfile', 'CSV file') }}
        {{ Form::file('csvfile'); }}
        {{ $errors->first('csvfile', '<p class="help-block">:message</p>') }}
    </div>

    {{ Form::submit('Save', ['class'=>'btn btn-pivot']); }}
    {{ link_to("/school/view/{$school->id}", 'Cancel', array('class'=>'btn btn-link')) }}
</fieldset>
{{ Form::close() }}

@stop