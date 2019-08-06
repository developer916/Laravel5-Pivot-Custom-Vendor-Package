@extends('layout-basic')

@section('content')
{{ Form::model($cycle, array('route' => ['cycle.save', $school->id, $cycle->id])) }}
    <fieldset>
    <legend>{{ $cycle->id ? 'Edit' : 'Add new' }} cycle</legend>

    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        {{ Form::label('name', 'Name') }}
        {{ Form::text('name', null, ['class' => 'form-control']) }}
        {{ $errors->first('name', '<p class="help-block">:message</p>') }}
    </div>

    <div class="form-group{{ $errors->has('start_date') ? ' has-error' : '' }}">
        {{ Form::label('start_date', 'Start date') }}
        <div class='input-group date' id='start_date' class="date">
            {{ Form::text('start_date', null, ['class' => 'form-control']) }}
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
        {{ $errors->first('start_date', '<p class="help-block">:message</p>') }}
    </div>

    <div class="form-group{{ $errors->has('end_date') ? ' has-error' : '' }}">
        {{ Form::label('end_date', 'End date') }}
        <div class='input-group date' id='end_date' class="date">
            {{ Form::text('end_date', null, ['class' => 'form-control']) }}
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
        {{ $errors->first('end_date', '<p class="help-block">:message</p>') }}
    </div>

    <script type="text/javascript">
        $(function () {
            $('.date').datepicker({format: 'dd/mm/yyyy'});
        });
    </script>

    {{ Form::submit('Save', ['class'=>'btn btn-pivot']); }}
    {{ link_to("/school/view/{$school->id}", 'Cancel', array('class'=>'btn btn-link')); }}
</fieldset>
{{ Form::close() }}

@stop