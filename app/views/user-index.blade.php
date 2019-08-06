@extends('layout-basic')

@section('content')
@if(Session::get('message'))
  <div class="alert alert-dismissable alert-success">
    <button data-dismiss="alert" class="close" type="button">×</button>
    {{ Session::get('message') }}
  </div>
@endif

{{ Form::open(array('method' => 'get')) }}
    <div class="row">
        <div class="col-sm-1">
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}" style="text-align:right;margin-top: 5px">
                {{ Form::label('email', 'Email') }}
            </div>
        </div>
        <div class="col-sm-9">
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                {{ Form::text('email', $email, ['class' => 'form-control']) }}
                {{ $errors->first('email', '<p class="help-block">:message</p>') }}
            </div>
        </div>
        <div class="col-sm-2">
            {{ Form::submit('Filter', ['class'=>'btn btn-pivot']); }}
        </div>
    </div>
{{ Form::close() }}

<div class="table-responsive">
    <table class="table table-hover">
        <tr>
            <th>School</th>
            <th>Name</th>
            <th>Role</th>
            <th>Email</th>
            <th></th>
            <th></th>
        </tr>
        @foreach($users as $user)
            <tr>
                <td>
                    @if ($user->school)
                        <a title="{{ $user->school->name }}" href={{ route('school.view', [$user->school->id]) }}>
                            <img src="{{ $user->school->logo->url('thumb'); }}" alt="{{ $user->school->name }}" />
                        </a>
                    @else
                        <img src="/images/logo-thumb.png" alt="Pivot" />
                    @endif
                </td>
                <td>
                    @if (UsersController::can_access($user))
                        {{ link_to("/user/view/$user->id", $user->name); }}
                    @else
                        {{ $user->name }}
                    @endif
                </td>
                <td>{{ ucfirst(str_replace('_', ' ', $user->role)); }}</td>
                <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                <td>
                    @if($user->role != User::PIVOT_ADMIN && $user->id != Auth::user()->id)
                    {{ link_to("/user/login_as/$user->id", 'Login as'); }}
                    @endif
                </td>
                <td>
                    {{ link_to("/user/edit/{$user->id}", '', array('class'=>'glyphicon glyphicon-edit', 'title'=>'Edit')); }}
                    {{ link_to("/user/delete/$user->id", '', array('class'=>'glyphicon glyphicon-remove', 'title'=>'Delete',
                        'data-toggle'=>'confirmation', 'data-placement' => 'left')); }}
                </td>
            </tr>
        @endforeach
    </table>
</div>
{{ link_to("/user/create", 'Add a user', array('class'=>'btn btn-pivot')); }}
@stop