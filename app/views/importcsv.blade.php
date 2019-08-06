@extends('layout-basic')

@section('content')
{{ Form::model($school, array('route' => ['school.savecsv', $school->id], 'method' => 'POST', 'files' => true)) }}
    <fieldset>
    <legend>Upload school CSV
        <span class="glyphicon glyphicon-question-sign help_button" data-toggle="modal" data-target="#ImportCSVModal" >
        </span>
    </legend>
    <div class="modal fade" id="ImportCSVModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Help</h4>
                </div>
                <div class="modal-body">
                    {{ $importcsvmodal }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

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