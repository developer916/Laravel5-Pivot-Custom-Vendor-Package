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
        <p class="hidden-print">{{ link_to("/reports/bar_graph_school_admin/$teacher_id/$cycle->id/$display_mode", 'Show '.$display_mode_text.' Breakdown', array('class'=>'btn btn-pivot')); }}</p>
    @endif
    <!-- SVG solution for correct printing-->
    <div id='schoolAvgScoresDiv' class="row" style="width: 100%; height: 40px; margin: 0;">
            <svg id='schoolAvgScores' style="width: 150px; height: 40px; padding-left:0px; padding-right:0px;">
                <rect width="150px" y="8px" height="24px" fill="#FAC589"/>
                <text fill="white" y="8px" dy='1.2em' x='10px' style="font-size: 14px;">School avg score:</text>
            </svg>
            <svg class="score tooltips" style="position: absolute; width: 100px; height: 40px;" data-toggle="tooltip" data-placement="bottom" data-original-title="The school's average score for Standard 1, '{{ $standards[0] }}', is {{ $averages[0] }}.">
                <rect width="35px" y="8px" height="24px" fill="#FAC589"/>
                <text fill="white" y="8px" dy='1.2em' x='10px' style="font-size: 14px; cursor: default;">1</text>
                <circle cx="50px" cy="20px" r="20px" fill="#f7ac5f"/>
                <text fill="white" y="8px" dy='1.2em' x='50px' style="font-size: 14px; cursor: default; text-anchor: middle; font-weight: bold;">{{ $averages[0] }}</text>
            </svg>
            <svg class="score tooltips" style="position: absolute; width: 100px; height: 40px;" data-toggle="tooltip" data-placement="bottom" data-original-title="The school's average score for Standard 2, '{{ $standards[1] }}', is {{ $averages[1] }}.">
                <rect width="35px" y="8px" height="24px" fill="#FAC589"/>
                <text fill="white" y="8px" dy='1.2em' x='10px' style="font-size: 14px; cursor: default;">2</text>
                <circle cx="50px" cy="20px" r="20px" fill="#f7ac5f"/>
                <text fill="white" y="8px" dy='1.2em' x='50px' style="font-size: 14px; cursor: default; text-anchor: middle; font-weight: bold;">{{ $averages[1] }}</text>
            </svg>
            <svg class="score tooltips" style="position: absolute; width: 100px; height: 40px;" data-toggle="tooltip" data-placement="bottom" data-original-title="The school's average score for Standard 3, '{{ $standards[2] }}', is {{ $averages[2] }}.">
                <rect width="35px" y="8px" height="24px" fill="#FAC589"/>
                <text fill="white" y="8px" dy='1.2em' x='10px' style="font-size: 14px; cursor: default;">3</text>
                <circle cx="50px" cy="20px" r="20px" fill="#f7ac5f"/>
                <text fill="white" y="8px" dy='1.2em' x='50px' style="font-size: 14px; cursor: default; text-anchor: middle; font-weight: bold;">{{ $averages[2] }}</text>
            </svg>
            <svg class="score tooltips" style="position: absolute; width: 100px; height: 40px;" data-toggle="tooltip" data-placement="bottom" data-original-title="The school's average score for Standard 4, '{{ $standards[3] }}', is {{ $averages[3] }}.">
                <rect width="35px" y="8px" height="24px" fill="#FAC589"/>
                <text fill="white" y="8px" dy='1.2em' x='10px' style="font-size: 14px; cursor: default;">4</text>
                <circle cx="50px" cy="20px" r="20px" fill="#f7ac5f"/>
                <text fill="white" y="8px" dy='1.2em' x='50px' style="font-size: 14px; cursor: default; text-anchor: middle; font-weight: bold;">{{ $averages[3] }}</text>
            </svg>
            <svg class="score tooltips" style="position: absolute; width: 100px; height: 40px;" data-toggle="tooltip" data-placement="bottom" data-original-title="The school's average score for Standard 5, '{{ $standards[4] }}', is {{ $averages[4] }}.">
                <rect width="35px" y="8px" height="24px" fill="#FAC589"/>
                <text fill="white" y="8px" dy='1.2em' x='10px' style="font-size: 14px; cursor: default;">5</text>
                <circle cx="50px" cy="20px" r="20px" fill="#f7ac5f"/>
                <text fill="white" y="8px" dy='1.2em' x='50px' style="font-size: 14px; cursor: default; text-anchor: middle; font-weight: bold;">{{ $averages[4] }}</text>
            </svg> 
            <script type="text/javascript">
                function positionScores() {
                    var width  = document.getElementById('schoolAvgScoresDiv').offsetWidth;
                    var scores = document.getElementById('schoolAvgScoresDiv').querySelectorAll('.score');
                    for ( var i = 0; i < scores.length; ++i ) {
                        var x = 130 + 0.09*width + i*(width-150)/5;
                        scores[i].style.left = x+"px";
                    }
                }
                positionScores();
                window.addEventListener('resize', positionScores );
            </script>
    </div>
    @foreach ($question_blocks as $title => $questionblock)
        <div class="row questionblock">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 block_title">{{ $title }}</div>
            @if(!empty($questionblock['aggraverages']))
            <div id="container_block{{ $questionblock['qnumber'] }}" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 barGraph"
                 style="height: 400px; margin: 0 auto; margin-bottom:10px;"></div>
            @else
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><h3>N/A</h3></div>
            @endif
        </div>

    @endforeach

    <!--div id="instructions">
        <p class="hidden-print"><span style="font-weight: bold;">How you can manipulate these graphs:</span><br/>
        Hover your mouse over a bar on any grah to see the department's average score for that standard<br/>
        Hover your mouse over the standard number to see the full title of that standard</p>
        <p><span style="font-weight: bold;">These graphs are useful for seeing:</span><br/>
        At a glance, each {{ $data_type }}'s average scores for each standard, as against the school average (the circle)</p>
    </div -->
    <div id="circle_desc">
        <div id="circle"><svg width="100%" height="100%">
            <circle cx="50%" cy="50%" r="50%" fill="#f7ac5e"/>
        </svg></div>
        <div id="desc_text">School average for each standard</div>
    </div>

@stop