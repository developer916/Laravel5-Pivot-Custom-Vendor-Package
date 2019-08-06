@extends('layout')

@section('content')
    <div class="title">
        <h4 class="panel-heading" style="padding:10px 0 5px 0;margin:0">{{$report_title}}</h4>
        <h5 class="panel-heading" style="padding:10px 0 5px 0;margin:0">{{$report_subtitle}}</h5>
        <h5 class="panel-heading" style="padding:10px 0 5px 0;margin:0">{{$report_cycletitle}}</h5>
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
    @if(isset($display_mode))
        <p class="hidden-print">{{ link_to("/reports/scatter_plot_school_admin/$teacher_id/$cycle->id/$display_mode",'Show '.$display_mode_text.' Breakdown', array('class'=>'btn btn-pivot hidden-print')); }}</p>
    @endif
    <div id="container" style="width: 970px; height: 800px; margin: 0 auto; padding-bottom:100px; border-bottom:1px solid #96999C;"></div>
    <!-- div>
    <p class="desc_title">This graph is useful for seeing:</p>
    <ul>
        <li>whole school trends per question, indicating systemic strengths and weaknesses</li>
        <li>top/bottom performing {{ $data_type }} across all questions and standards</li>
    </ul>
    <p class="desc_title hidden-print">How you can manipulate this graph:</p>
    <ul>
        <li>Hover your mouse over a question number (on the bottom (x) axis) to show up the question title</li>
        <li>Click on a department in the legend to hide/unhide it's results</li>
        <li>Hover your mouse over a selected {{ $data_type }}'s point, at a specific question, to see that question's score</li>
        <li>Note that the dotted lines separate out questions belonging to each of the five standards.</li>
    </ul>
    </div -->
@stop