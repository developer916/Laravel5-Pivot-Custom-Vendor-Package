@extends('layout-basic')

@section('content')
@if(Session::get('message'))
  <div class="alert alert-dismissable alert-success">
    <button data-dismiss="alert" class="close" type="button">×</button>
    {{ Session::get('message') }}
  </div>
@endif
<div class="row show-grid">
    <div class="col-md-4 ">
        <div class="panel panel-pivot">
            <div class="panel-heading">
                Please enter your email address
            </div>
            <div class="panel-body">
            {{ Form::model(null, array('route' => 'remind.send', 'method' => 'POST')) }}
            <fieldset>

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    {{ Form::label('email', 'Email') }}
                    {{ Form::text('email', null, ['class' => 'form-control']) }}
                    {{ $errors->first('email', '<p class="help-block">:message</p>') }}
                </div>

                {{ Form::submit('Submit', ['class'=>'btn btn-pivot']); }}
                {{ link_to("/login", 'Cancel', array('class'=>'btn btn-link')); }}

            </fieldset>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop