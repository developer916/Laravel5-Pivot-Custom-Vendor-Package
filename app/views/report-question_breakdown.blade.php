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
        <p class="hidden-print">{{ link_to("/reports/question_break_down_school_admin/$teacher_id/$cycle->id/$display_mode", 'Show '.$display_mode_text.' Breakdown', array('class'=>'btn btn-pivot hidden-print')); }}</p>
    @endif
    <ul id="myTab" class="nav nav-tabs hidden-print" role="tablist">
        <?php $first = true; ?>
        @foreach($question_groups as $group)
            <li role="presentation"
                @if($first)
                    {{ 'class="active"' }}
                @endif
            >
                <a id="standard{{ $group['standard_number'] }}-tab"
                @if($first)
                    {{ 'aria-expanded="true"' }}
                @endif
                aria-controls="standard{{ $group['standard_number'] }}" role="tab" href="#standard{{ $group['standard_number'] }}" onClick="">Standard {{ $group['standard_number'] }}</a>
            </li>
            <?php $first = false; ?>
        @endforeach
    </ul>
    <div id="tab-content" class="tab-content" >
    <?php $first = true; ?>
    <!-- Note: make everything active so that the content rendered. We then remove the active class in js -->
    @foreach($question_groups as $group)
    <div id="standard{{ $group['standard_number'] }}" class="question_group tab-pane active breakdownGroup" role="tabpanel" >
        <div class='breakdownHeader' idx="{{ $group['standard_number'] }}" style="page-break-inside: avoid; width: 100%;">
        <div style="width: 100%; height: 30px">
            <svg width="100%" height="100%" >
                <rect class="svgbkg" width="100%" height="100%" fill="#EBEEF5"></rect>
                <rect width="150px" height="100%" fill="#7694BF"></rect>
                <text fill="white" x="15px" y="5px" dy="1em" style="font-size: 11pt">Standard {{ $group['standard_number'] }}:</text>
                <text x="160px" y="5px" dy="1em" style="font-size: 11pt">{{ $group['standard'] }}</text>
            </svg>
        </div>

        <div style="width: 100%; height: 30px; margin-top: 3px">
            <svg width="100%" height="100%" >
                <rect class="svgbkg" width="100%" height="100%" fill="#73659B"></rect>
                <rect width="330px" height="100%" fill="#9D93B9"></rect>
                <text fill="white" x="15px" y="5px" dy="1em" style="font-size: 11pt">Highest Scoring {{ $aggr_type }} for Standard {{ $group['standard_number'] }}</text>
                <text fill="white" x="340px" y="5px" dy="1em" style="font-size: 11pt">{{ $high_standards ? $high_standards[$group['standard_number']][0] : ''}}  |  {{ $high_standards ? $high_standards[$group['standard_number']][1] : '' }} average</text>
            </svg>
        </div>

        <div style="width: 100%; height: 30px; margin-top: 3px; margin-bottom: 15px">
            <svg width="100%" height="100%" >
                <rect class="svgbkg" width="100%" height="100%" fill="#73659B"></rect>
                <rect width="330px" height="100%" fill="#9D93B9"></rect>
                <text fill="white" x="15px" y="5px" dy="1em" style="font-size: 11pt">Lowest Scoring {{ $aggr_type }} for Standard {{ $group['standard_number'] }}</text>
                <text fill="white" x="340px" y="5px" dy="1em" style="font-size: 11pt">{{ $low_standards ? $low_standards[$group['standard_number']][0] : '' }}  |  {{ $low_standards ? $low_standards[$group['standard_number']][1] : '' }} average</text>
            </svg>
        </div>
        </div>
        @foreach($group['questions'] as $question)
        <div style="width: 100%; height: 460px; margin-top: 0px; margin-bottom: 0px; page-break-inside: avoid;">
            <div class="question_number " style="float: left; width: 10%; height: 100%; text-align: center; ">
                <svg width="100%" height="40px"><text x="50%" dy="1em" font-size="25px" fill="#B8B3CD" text-anchor="middle">{{ $question['number'] }}</text></svg>
            </div>
            <div style="float: left; width: 90%; height: 100%; ">
                
                <div class="question_text" style="float: left; width: 100%; height: 6%; ">{{ $question['text'] }}</div>
                
                <div style="float: left; width: 10%; height: 80%; border-right: 1px solid #D2D3D4; ">
                    <div class="question_statistics">
                        <div style="margin-right: 0px">
                            <div class="question_box_text1">{{ $question['average2_label'] }}</div>
                            <div class="question_box_text2">average</div>
                            <div class="circle">
                                <svg width="100%" height="100%">
                                    <circle cx="50%" cy="50%" r="50%" fill="#f7ac5e"/>
                                    <text x="50%" y="50%" dy="0.35em" text-anchor="middle" fill="white" font-size="16pt">{{ $question['average2_value'] }}</text>
                                </svg>
                            </div>
                        </div>
                        @if(isset($question['average1_label']))
                        <div>
                            <div class="question_box_text1">{{ $question['average1_label'] }}</div>
                            <div class="question_box_text2">average</div>
                            <div class="box">
                                <svg width="100%" height="100%">
                                    <rect width="100%" height="100%" fill="#72699b"/>
                                    <text x="50%" y="50%" dy="0.35em" text-anchor="middle" fill="white" font-size="16pt">{{ $question['average1_value'] }}</text>
                                </svg>
                            </div>
                        </div>
                        @endif
                        @if(isset($question['average3_label']))
                            <div>
                                <div class="question_box_text1">{{$question['average3_label']}}</div>
                                <div class="question_box_text2">previous survey average</div>
                                <div class="box_last">
                                    <svg width="100%" height="100%">
                                        <rect width="100%" height="100%" fill="#d4d5d6"/>
                                        <text x="50%" y="50%" dy="0.35em" text-anchor="middle" fill="white" font-size="16pt">{{ $question['average3_value'] }}</text>
                                    </svg>
                                </div>

                            </div>
                        @endif

                        <div class="total_responses" style="width: 100%">
                            Total responses <br/> <span style="width: 100%; font-weight:bold; font-size:12px;">{{ $question['total_responses'] }} </span>
                        </div>
                    </div>
                </div>
                
                <div style="float: left; width: 60%; height: 80%; ">
                    <div style="float: left; width: 100%; height: 10%;">
                        <div class="question_content_text1">{{ $question_content2 }}</div>
                        @if(isset($question['average3_label']))
                        <div class="question_content_key_previous">
                            <svg width="10px" height="10px" style="margin-top: 5px; margin-right: 10px"><rect width="100%" height="100%" fill="#d4d5d6"/></svg><span class="text-muted">Previous survey results</span>
                        </div>
                        @endif
                    </div>
                    <div class="question_chart" id="chart{{ $question['number'] }}" style="float: left; width: 100%; height: 90%;"></div>
                </div>

                <div style="float: left; width: 30%; height: 80%; ">
                    <svg id="right-chart" width="100%" height="100%" style="float: left;">
                        <text x="50%" y="5px" dy="1em" text-anchor="middle" fill="#333333">Distribution of scores</text>
                        <text x="50%" y="25px" dy="1em" text-anchor="middle" fill="#777777">number of responses</text>
                        
                        "{{ $question['chart_html'] }}"
                        <!-- <rect x="10%" y="105px" width="15%" height="85px" fill="#B8B0CC"/>
                        <rect x="26%" y="105px" width="15%" height="85px" fill="#B8B0CC"/>
                        <rect x="42%" y="105px" width="15%" height="85px" fill="#B8B0CC"/>
                        <rect x="58%" y="105px" width="15%" height="85px" fill="#B8B0CC"/>
                        <rect x="74%" y="105px" width="15%" height="85px" fill="#B8B0CC"/> -->

                        <text y="200px" x="10%" dy="1em" text-anchor="start" fill="#333333">Strongly</text>
                        <text y="200px" x="10%" dy="2em" text-anchor="start" fill="#333333">disagree</text>

                        <text y="200px" x="89%" dy="1em" text-anchor="end" fill="#333333">Strongly</text>
                        <text y="200px" x="89%" dy="2em" text-anchor="end" fill="#333333">agree</text>
                    </svg>
                </div>

                @if(isset($question['self_assessment']))
                <div style="float: left; width: 100%; height: 6%; ">
                    <div style="width: 100%; height: 30px; margin-top: 10px">
                        <svg width="100%" height="100%" style="float: left;">
                            <rect class="svgbkg" width="250px" height="100%" fill="#E6E7E8"></rect>
                            <rect width="30px" height="100%" fill="#D4D5D6"></rect>
                            <text fill="black" x="15px" text-anchor="middle" y="5px" dy="1em" style="font-size: 11pt">{{ $question['self_assessment'] }}</text>
                            <text fill="#818386" x="40px" y="5px" dy="1em" style="font-size: 11pt">Teacher's self assessment</text>
                        </svg>
                    </div>
                </div>
                @endif
                <div style="float: left; border-bottom: 1px solid #D2D3D4; width: 100%; height: 4%;"></div>
            </div>
        </div>
        <?php $first = false; ?>
        @endforeach
    </div>
    <div class="page-break"></div>
    @endforeach
    </div>
    <script type="text/javascript">
        var divs = document.querySelectorAll(".breakdownHeader");
        for ( var i = 0; i < divs.length; ++i ) {
            var idx = +divs[i].getAttribute('idx');
            if ( idx !== 1 ) divs[i].style.pageBreakBefore = 'always';
        }
    </script>
    <script type="text/javascript">
        function setSvgSizeForPrinting(){
            var chartDivs = document.querySelectorAll(".highcharts-container");
            for ( var i = 0; i < chartDivs.length; ++i ) {
                chartDivs[i].style.width="100%";
                chartDivs[i].style.height="100%";
                var svg = chartDivs[i].querySelector("svg");

                if ( svg.getAttribute("width") == '100%' ) continue;

                svg.setAttribute("viewBox", "0 0 "+svg.getAttribute("width") + " " + svg.getAttribute("height") );
                svg.setAttribute("width", "100%");
                svg.setAttribute("height", "100%");
            }
        }

        setTimeout( setSvgSizeForPrinting, 200);
        window.addEventListener('resize',setSvgSizeForPrinting);
        
    </script>
@stop