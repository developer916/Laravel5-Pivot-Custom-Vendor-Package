@extends('layout-basic')

@section('content')
{{ Form::model($selected, array('route' => ['cycle_class.save', $cycle->id])) }}
<fieldset>
    <legend>{{ $header }}</legend>

    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        {{ Form::label('classes', 'Classes') }}
        {{ Form::select('classes[]', $classes, null, ['id'=>'classes', 'class' => 'form-control', 'placeholder' => 'Select one or more classes', 'multiple']) }}
        {{ $errors->first('classes', '<p class="help-block">:message</p>') }}
    </div>
    <div class="form-group">
        {{ Form::checkbox('selectall', '1', false, ['id'=>'selectall']) }}
        {{ Form::label('selectall', 'Select All Classes') }}
    </div>
    <script type="text/javascript">
        $('#classes').select2();
        $("#selectall").click(function(){
            if($("#selectall").is(':checked') ){
                $("#classes option").prop("selected","selected");
                $("#classes").trigger("change");
            }else{
                $("#classes option").removeAttr("selected");
                 $("#classes").trigger("change");
             }
        });
    </script>
    {{ Form::submit('Save', ['class'=>'btn btn-pivot']); }}
    {{ link_to("/cycle/view/{$cycle->id}", 'Cancel', array('class'=>'btn btn-link')); }}
</fieldset>
{{ Form::close() }}

@stop