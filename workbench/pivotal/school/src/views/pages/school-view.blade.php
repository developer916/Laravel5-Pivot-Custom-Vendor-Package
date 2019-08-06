@extends('layout-basic')

@section('content')
@if(Session::get('errors'))
    @foreach(Session::get('errors')->getBags() as $error_group)
        @foreach($error_group->getMessages() as $error)
            <div class="alert alert-dismissable alert-danger">
                <button data-dismiss="alert" class="close" type="button">×</button>
                {{$error[0]}}
            </div>
        @endforeach
    @endforeach
@endif
@if(Session::get('error'))
  <div class="alert alert-dismissable alert-danger">
    <button data-dismiss="alert" class="close" type="button">×</button>
    {{ Session::get('error') }}
  </div>
@endif
@if(Session::get('message'))
  <div class="alert alert-dismissable alert-success">
    <button data-dismiss="alert" class="close" type="button">×</button>
    {{ Session::get('message') }}
  </div>
@endif

<div class="row show-grid">
    <div class="col-md-2">
        <td rowspan="2"><img class="img-responsive img-rounded" src="{{ $school->logo->url('medium'); }}" /></td>
    </div>
    <div class="col-md-4">
        <table class="table table-condensed table-pivot">
            <tr>
                <th>Name</th>
                <td>{{ $school->name }}</td>
            </tr>
            <tr>
                <th>Abbr</th>
                <td>{{ $school->abbr }}</td>
            </tr>
        </table>
        @if (Auth::user()->isSuperAdmin())
            <p>
                {{ link_to("/school/edit/{$school->id}", 'Edit school', array('class'=>'btn btn-pivot')); }}
            </p>
            <p>
                <a href="{{\URL::route('admin.user.import.meta',$school->id)}}" class="btn btn-pivot">Upload User Meta CSV</a>
            </p>
            <p>
                {{ link_to("/school/importcsv/{$school->id}", 'Upload school CSV', array('class'=>'btn btn-pivot')); }}
            </p>
            <p>
                {{ link_to("/reports/full/csv/{$school->id}", 'Survey Data CSV', array('class'=>'btn btn-pivot')); }}
            </p>
        @endif
    </div>
    <div class="col-md-6 welcome_message">
        @include('welcome-message') 
    </div>
</div><br/>
@if(\Auth::user()->proxies->count() > 0)
<div class="row show-grid">
    <div class="col-xs-5">
        <div class="panel panel-pivot">
            <div class="col-xs-7 panel-heading">
                Alternate Accounts
            </div>
        </div>
        <div class="panel-body col-xs-12">
            @foreach(Auth::user()->proxies as $proxy)
                <div class="col-xs-12"><a href="{{URL::route('user.login.as',$proxy->id)}}">{{$proxy->name}}</a></div>
            @endforeach
        </div>
    </div>
</div>
@endif
<div class="row show-grid">
    <div class="col-md-12">
        {{ $cycles_panel }}
    </div>
    <div class="col-md-4">
        {{ $departments_panel }}
    </div>
    <div class="col-md-4">
        @if (count($campuses) > 0)
            <div id="campusToggler" style="margin-bottom:10px">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-primary active">
                        <input name="campus" value="0" type="checkbox" autocomplete="off" checked> No Campus
                    </label>
                    @foreach($campuses as $campus)
                        <label class="btn btn-primary active">
                            <input type="checkbox"  name="campus" value="{{ $campus->id }}" checked autocomplete="off"> {{ $campus->code }}
                        </label>
                    @endforeach
                </div>
            </div>
            <script type="text/javascript">
                $(function () {
                    $('#campusToggler input[name="campus"]').change(function() {
                        var campuses = $('#campusToggler input[name="campus"]:checked').map(function() {
                            return this.value;
                        }).get();
                        $('#classesList tr').hide();
                        for (var i = 0; i < campuses.length; i++) {
                            var t = campuses[i];
                            if (t == '0') {
                                $('#classesList tr').show();
                                return true;
                            }
                            $('#classesList tr[data-campus=' + t + ']').show();
                        }
                    });
                });
            </script>
        @endif
        {{ $classes_panel }}
    </div>
    <div class="col-md-4">
        {{ $teachers_panel }}
    </div>
</div>
@stop