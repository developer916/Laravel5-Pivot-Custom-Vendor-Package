@extends('layout-basic')

@section('content')
@if(Session::get('message'))
  <div class="alert alert-dismissable alert-success">
    <button data-dismiss="alert" class="close" type="button">×</button>
    {{ Session::get('message') }}
  </div>
@endif
<div class="table-responsive">
    <table class="table table-hover">
        <tr>
            <th style="width: 15%" rowspan="2">Logo</th>
            <th rowspan="2">School  <a href="?sort_by=name&sort_asc=1">&darr;</a> <a href="?sort_by=name&sort_asc=0">&uarr;</a></th>
            <th rowspan="2">Abbr</th>
            <th colspan="3" style="text-align: center">Last cycle</th>
            @if (Auth::user()->isEditor())
                <th rowspan="2"></th>
            @endif
        </tr>
        <tr>
            <th>created <a href="?sort_by=created&sort_asc=1">&darr;</a> <a href="?sort_by=created&sort_asc=0">&uarr;</a></th>
            <th>start <a href="?sort_by=start&sort_asc=1">&darr;</a> <a href="?sort_by=start&sort_asc=0">&uarr;</a></th>
            <th>end <a href="?sort_by=end&sort_asc=1">&darr;</a> <a href="?sort_by=end&sort_asc=0">&uarr;</a></th>
        </tr>
        @foreach($schools as $school)
            <tr>
                <td>
                    <a title="{{ $school->name }}" href={{ route('school.view', [$school->id]) }}>
                        <img class="img-rounded school-logo" src="{{ $school->logo->url('thumb'); }}" alt="{{ $school->name }}" style="max-width:100px;" />
                    </a>
                </td>
                <td>
                    @if (Pivotal\School\Controllers\SchoolController::can_access($school))
                        {{ link_to("/school/view/$school->id", $school->name); }}
                    @else
                        {{ $school->name }}
                    @endif
                </td>
                <td>{{ $school->abbr }}</td>
                    @if (count($school->lastCycle) > 0)
                        <td>
                            {{ date('d/m/Y', strtotime($school->lastCycle->get(0)->created_at)) }}
                        </td>
                        <td>
                            {{ $school->lastCycle->get(0)->start_date }}
                        </td>
                        <td>
                            {{ $school->lastCycle->get(0)->end_date }}
                        </td>
                    @else
                        <td colspan="3"><em>no cycles yet</em></td>
                    @endif

                @if (Auth::user()->isEditor())
                    <td>
                        {{ link_to("/school/edit/$school->id", '', array('class'=>'glyphicon glyphicon-edit', 'title'=>'Edit')); }}
                        {{ link_to("/school/delete/$school->id", '', array('class'=>'glyphicon glyphicon-remove', 'title'=>'Delete',
                            'data-toggle'=>'confirmation', 'data-placement' => 'left')); }}
                    </td>
                @endif
            </tr>
        @endforeach
    </table>
</div>
@if (Auth::user()->isEditor())
    {{ link_to("/school/edit", 'Add a school', array('class'=>'btn btn-pivot')); }}
@endif
@stop