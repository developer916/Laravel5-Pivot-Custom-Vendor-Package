@extends('layout')

@section('content')

    <div class="row">
        <div id="heatmap_intro" class="col-md-11">{{ $opening_text }}</div>
    </div>
    <button id="report_info" type="button" class="btn btn-info hidden-print" data-toggle="modal" data-target="#myModal">
        <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Additional info
    </button>
     <br class="remove_br"/>
    <button class="btn btn-primary btn-small printing_btn" onClick="window.print()">Print</button><br class="remove_br"/><br class="remove_br"/>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Additional Info</h4>
                </div>
                <div class="modal-body">
                    {{ $additional_info }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="container" style="width: 1140px; height: 800px; margin: 0 auto; margin-bottom:100px;"></div>

    <div style="width: 100%; height: {{30 + 15*count( $areas_of['strength'])}}px">
        <svg width="100%" height="100%" style="float: left;">
            <rect class="svgbkg" width="100%" height="100%" fill="#ededee"></rect>
            <rect width="150px" height="100%" fill="#6084B0"></rect>
            <text fill="white" x="15px" y="{{(30+15*count( $areas_of['strength']))/2}}px" dy="0.25em" style="font-size: 11pt">Area of strength</text>
            @for ($i = 0; $i < count( $areas_of['strength']); $i++)
                <text x="160px" y="10px" dy="{{1.1+$i*1.1}}em" style="font-size: 11pt">{{ $areas_of['strength'][$i] }}</text>
            @endfor
        </svg>
    </div>

    <table class="individual highest" style="width: 100%">
        <td colspan="3"><div class="area_of_statement" style="width: 100%">Highest individual question scores</div></td>
        @foreach($high_individual as $individual)
        <tr>
            <td><div class="col1">{{ $individual['col1'] }}</div></td>
            <td><div class="col2">{{ $individual['col2'] }}</div></td>
            <td><div class="col3">{{ $individual['col3'] }}</div></td>
        </tr>
        @endforeach
    </table>
    <div style="width: 100%; height: {{30 + 15*count( $areas_of['growth'])}}px; margin: 0">
        <svg width="100%" height="100%" style="float: left;">
            <rect class="svgbkg" width="100%" height="100%" fill="#ededee"></rect>
            <rect width="150px" height="100%" fill="#9690B6"></rect>
            <text fill="white" x="15px" y="{{(30+15*count( $areas_of['growth']))/2}}px" dy="0.25em" style="font-size: 11pt">Area for growth</text>
            @for ($i = 0; $i < count( $areas_of['growth']); $i++)
                <text x="160px" y="10px" dy="{{1.1+$i*1.1}}em" style="font-size: 11pt">{{ $areas_of['growth'][$i] }}</text>
            @endfor
        </svg>
    </div>

    <table class="individual lowest" style="width: 100%">
        <td colspan="3"><div class="area_of_statement" style="width: 100%">Lowest individual question scores</div></td>
        @foreach($low_individual as $individual)
        <tr>
            <td><div class="col1">{{ $individual['col1'] }}</div></td>
            <td><div class="col2">{{ $individual['col2'] }}</div></td>
            <td><div class="col3">{{ $individual['col3'] }}</div></td>
        </tr>
        @endforeach
    </table>
    @if(isset($improvement_data))
        <table class="individual improvement">
            <td colspan="3"><div class="area_of_statement">Greatest Improvement in individual questions</div></td>
            @foreach($improvement_data as $qid => $value)
                <tr>
                    <td><div class="col1">{{ $value['value'] }}</div></td>
                    <td><div class="col2">Q{{ $value['index'] }}</div></td>
                    <td><div class="col3">{{$heatmap_data['questions'][$qid]}}</div></td>
                </tr>
            @endforeach
        </table>
    @endif
@stop