@extends('layout-basic')

@section('content')
<div class="row show-grid">
    <div class="col-md-4 ">
        <div class="panel panel-pivot panel-login">
            <div class="panel-heading">
                Login
            </div>
            <div class="panel-body">
            {{ Form::model(null, array('url' => URL::route('user.login', array(), true), 'method' => 'POST', 'files' => true)) }}
            <fieldset>

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    {{ Form::label('email', 'Email') }}
                    {{ Form::text('email', null, ['class' => 'form-control']) }}
                    {{ $errors->first('email', '<p class="help-block">:message</p>') }}
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    {{ Form::label('password', 'Password') }}
                    {{ Form::password('password', ['class' => 'form-control']) }}
                    {{ $errors->first('password', '<p class="help-block">:message</p>') }}
                </div>

                {{ Form::submit('Submit', ['class'=>'btn btn-pivot']); }}
                {{ link_to("/password/remind", 'Reset my password', array('class'=>'btn btn-link')); }}

            </fieldset>
            {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@stop