@extends('layout-basic')

@section('content')
@if(Session::get('error'))
  <div class="alert alert-dismissable alert-danger">
    <button data-dismiss="alert" class="close" type="button">×</button>
    {{ Session::get('error') }}
  </div>
@endif
<div class="row show-grid">
    <div class="col-md-4 ">
        <div class="panel panel-pivot">
            <div class="panel-heading">
                Reset password
            </div>
            <div class="panel-body">
                {{ Form::model(null, array('route' => 'reset.process', 'method' => 'POST')) }}
                    <fieldset>
                        <input type="hidden" name="token" value="{{ $token }}">

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

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            {{ Form::label('password_confirmation', 'Confirm password') }}
                            {{ Form::password('password_confirmation', ['class' => 'form-control']) }}
                            {{ $errors->first('password_confirmation', '<p class="help-block">:message</p>') }}
                        </div>
                        <div id="password-strength">

                        </div>
                        {{ Form::submit('Submit', ['class'=>'btn btn-pivot']); }}
                    </fieldset>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        $('#password').pwstrength();
    });
</script>
@stop