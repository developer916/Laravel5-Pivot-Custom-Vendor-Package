@extends('layout-basic')

@section('content')
@if(Session::get('message'))
  <div class="alert alert-dismissable alert-success">
    <button data-dismiss="alert" class="close" type="button">×</button>
    {{ Session::get('message') }}
  </div>
@endif
<div class="row show-grid">
    <div class="col-md-2">
        <img class="img-responsive img-rounded school-logo" title="{{ $cycle->school->name }}" src="{{ $cycle->school->logo->url('medium'); }}" />
    </div>
    <div class="col-md-4">
        <table class="table table-condensed table-pivot">
            <tr>
                <th>Name</th>
                <td>{{ $cycle->name }}</td>
            </tr>
            <tr>
                <th>Start date</th>
                <td>{{ $cycle->start_date; }}</td>
            </tr>
            <tr>
                <th>End date</th>
                <td>{{ $cycle->end_date; }}</td>
            </tr>
        </table>
    </div>
</div>
<div class="row show-grid">
    <div class="col-md-12">
        {{ $cyclesclasses_panel }}
    </div>
</div>
@if (Auth::user()->isSuperAdmin())
    <p>
        {{ link_to("/cycle/edit/{$cycle->school->id}/{$cycle->id}", 'Edit cycle', array('class'=>'btn btn-pivot')); }}
        {{ link_to("/cycle/class/edit/{$cycle->id}", 'Edit classes in cycle', array('class'=>'btn btn-pivot')); }}
    </p>
@endif
@stop