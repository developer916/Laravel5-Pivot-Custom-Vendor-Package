@extends('layout-basic')

@section('content')
{{ Form::model($user, array('route' => ['user.save', json_encode(array('school_id' => $user->school_id, 'user_id' => $user->id))], 'method' => 'POST', 'files' => true)) }}
    <fieldset>
    <legend>{{ $user->id ? 'Edit' : 'Add new' }} user</legend>

    <div class="form-group{{ $errors->has('school_id') ? ' has-error' : '' }}">
        {{ Form::label('school_id', 'School') }}
        @if ($user->school_id)
            {{ Form::text('school', $schools[$user->school_id], ['class' => 'form-control', 'readonly']) }}
            {{ Form::hidden('school_id', $user->school_id) }}
        @else
            {{ Form::select('school_id', $schools, null, ['class' => 'form-control']) }}
        @endif
        {{ $errors->first('school_id', '<p class="help-block">:message</p>') }}
    </div>

    <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
        {{ Form::label('role', 'Role') }}
        {{ Form::select('role', $roles, null, ['class' => 'form-control']) }}
        {{ $errors->first('role', '<p class="help-block">:message</p>') }}
    </div>

    <div class="form-group{{ $errors->has('department_id') ? ' has-error' : '' }}">
        {{ Form::label('department_id', 'Department') }}
        {{ Form::select('department_id', $departments, null, ['class' => 'form-control']) }}
        {{ $errors->first('department_id', '<p class="help-block">:message</p>') }}
        <p class="help-block">Only required if the user is a Department Head</p>
    </div>

    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        {{ Form::label('name', 'Name') }}
        {{ Form::text('name', null, ['class' => 'form-control']) }}
        {{ $errors->first('name', '<p class="help-block">:message</p>') }}
    </div>

    <div class="form-group required {{ $errors->has('email') ? ' has-error' : '' }}">
        {{ Form::label('email', 'Email') }}
        {{ Form::text('email', null, ['class' => 'form-control']) }}
        {{ $errors->first('email', '<p class="help-block">:message</p>') }}
    </div>

    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
        {{ Form::label('password', 'Password') }}
        {{ Form::password('password', ['class' => 'form-control']) }}
        {{ $errors->first('password', '<p class="help-block">:message</p>') }}
        @if ($user->id && !$errors->has('password'))
            <p class="help-block">Leave blank to keep existing password</p>
        @endif
    </div>

    <div class="form-group{{ $errors->has('password2') ? ' has-error' : '' }}">
        {{ Form::label('password2', 'Retype password') }}
        {{ Form::password('password2', ['class' => 'form-control']) }}
        {{ $errors->first('password2', '<p class="help-block">:message</p>') }}
    </div>

    {{ Form::submit('Save', ['class'=>'btn btn-pivot']); }}
    @if ($school)
        {{ link_to("/school/view/{$school->id}", 'Cancel', array('class'=>'btn btn-link')); }}
    @else
        {{ link_to("/users", 'Cancel', array('class'=>'btn btn-link')); }}
    @endif
</fieldset>
{{ Form::close() }}

@stop