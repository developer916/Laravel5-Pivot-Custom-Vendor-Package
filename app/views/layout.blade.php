<html>
    <head>
    {{ HTML::script('https://code.jquery.com/jquery-1.10.2.min.js') }}
    {{ HTML::style('bootstrap/css/bootstrap.min.css') }}
    {{ HTML::script('bootstrap/js/bootstrap.min.js') }}
    {{ HTML::style('css/styles.css?v=1') }}
    {{ HTML::style('css/chart.css?v=1') }}
    {{ HTML::style('css/print.css?v=1') }}
        <!--[if gte IE 9]>
        <style type="text/css">
            .gradient {
                filter: none;
            }
        </style>
        <![endif]-->
    <title>Pivot - {{ $header }}</title>
    </head>
    <body id="{{ $page }}">
        <div class="container">
            <div id="header_row" class="row">
                <div id="header_col" class="col-md-9">
                    <div id="header_div">
                        <h2 id="header"><span id="header_student">Pivot</span>
                            @if (Auth::check())
                                {{ link_to('/user/view/'.Auth::user()->id, Auth::user()->name, array('id'=>'subheader_bold')); }}
                                @if (Session::get('orig_id'))
                                    <span class="hidden-print">({{ link_to('/logout_as/', 'Back to Pivot Admin'); }})</span>
                                @else
                                    <span class="hidden-print">({{ link_to('/logout/', 'Logout'); }})</span>
                                @endif
                                {{ Auth::user()->school ? ' | '.Auth::user()->school->name : '' }}
                            @endif
                        </h2>
                    </div>
                </div>
                <div class="col-md-3 text-right">
                    {{ HTML::image('images/logo.png', 'logo') }}
                </div>
                <div class="printonly col-md-12">
                    <h3>
                        {{ Auth::user()->name }}, {{ Auth::user()->school->name }}
                    </h3>
                    <h4>
                        {{ $subheader_bold }}
                    </h4>
                    <div style="width:100%; border-bottom:2px solid grey;">&nbsp;</div>
                </div>
            </div>
            @if (Auth::check())
            <div class="row">
                <div class="col-md-12">
                    <div class="navbar navbar-pivot">
                        <div class="navbar-collapse collapse navbar-responsive-collapse">
                            <ul class="nav navbar-nav">
                                <li>
                                    <a href="/" title="Home">
                                        <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
                                    </a>
                                </li>
                                @if (Auth::user()->isAdministrator())
                                    <li class="{{ starts_with(Route::current()->getName(), 'school') ? 'active' : ''; }}">
                                        <a href="{{ route('school.index') }}">Schools</a>
                                    </li>
                                    <li class="{{ starts_with(Route::current()->getName(), 'user') ? 'active' : ''; }}">
                                        <a href="{{ route('user.index') }}">Users</a>
                                    </li>
                                @elseif (Auth::user()->school)
                                    @if (Pivotal\School\Controllers\SchoolController::can_access(Auth::user()->school))
                                        <li class="{{ starts_with(Route::current()->getName(), 'school') ? 'active' : ''; }} dropdown" style="display:none;">
                                            {{ link_to("/school/view/".Auth::user()->school->id, Auth::user()->school->name) }}
                                        </li>
                                    @endif
                                    @if (!empty($nav['departments']))
                                        <li class="{{ starts_with(Route::current()->getName(), 'department') ? 'active' : ''; }} dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Departments <b class="caret"></b></a>
                                            <ul class="dropdown-menu">
                                                @foreach ($nav['departments'] as $department)
                                                    <li>
                                                        <a href="/department/view/{{ $department->id }}">{{ $department->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                    @if (!empty($nav['classes']))
                                        <li class="{{ starts_with(Route::current()->getName(), 'class') ? 'active' : ''; }} dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Classes <b class="caret"></b></a>
                                            <ul class="dropdown-menu">
                                                @foreach ($nav['classes'] as $class)
                                                    <li>
                                                        <a href="/class/view/{{ $class->id }}">{{ $class->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                    @if (!empty($nav['teachers']))
                                        <li class="{{ starts_with(Route::current()->getName(), 'user') ? 'active' : ''; }} dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Teachers <b class="caret"></b></a>
                                            <ul class="dropdown-menu">
                                                @foreach ($nav['teachers'] as $teacher)
                                                    <li>
                                                        <a href="/user/view/{{ $teacher->id }}">{{ $teacher->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                    @if (!empty($nav['cycles']))
                                        <li class="{{ starts_with(Route::current()->getName(), 'report') ? 'active' : ''; }} dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reports <b class="caret"></b></a>
                                            <ul class="dropdown-menu">
                                                <?php
                                                $prevYear = 0;
                                                ?>
                                                @foreach ($nav['cycles'] as $cycle)
                                                    <?php
                                                        $start_date = \DateTime::createFromFormat('d/m/Y', $cycle->start_date);
                                                        $year = $start_date->format('Y');
                                                        if ($year != $prevYear && $year != date('Y')) {
                                                            echo '<li class="dropdown-header" style="font-weight: bold; color: #eee; font-style: italic; text-align: center;">'.$year.'</li>';
                                                            $prevYear = $year;
                                                        }
                                                    ?>
                                                    <li class="dropdown-submenu">
                                                        @if (Auth::user()->role == User::SCHOOL_ADMIN)
                                                        <a href="#">{{ $cycle->name }}</a>
                                                        <ul class="dropdown-menu">
                                                            <li class="dropdown-submenu"><a href="#">School reports</a>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a href="/reports/heatmap_school_admin/{{ Auth::user()->id }}/{{ $cycle->id }}">Summary - Heat Map</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="/reports/scatter_plot_school_admin/{{ Auth::user()->id }}/{{ $cycle->id }}">Scatter Report</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="/reports/comparison_table_school_admin/{{ Auth::user()->id }}/{{ $cycle->id }}">Grid Report</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="/reports/bar_graph_school_admin/{{ Auth::user()->id }}/{{ $cycle->id }}">Bar Graph</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="/reports/question_break_down_school_admin/{{ Auth::user()->id }}/{{ $cycle->id }}">Detailed Question Breakdown</a>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                            @foreach (Auth::user()->school->departments as $department)
                                                            <li class="dropdown-submenu"><a href="#">{{ $department->name }} Reports</a>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a href="/reports/heatmap_department_head/{{ $department->id }}/{{ $cycle->id }}">Summary - Heat Map</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="/reports/scatter_plot_department_head/{{ $department->id }}/{{ $cycle->id }}">Scatter Report</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="/reports/bar_graph_department_head/{{ $department->id }}/{{ $cycle->id }}">Bar Graph</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="/reports/question_break_down_department_head/{{ $department->id }}/{{ $cycle->id }}">Detailed Question Breakdown</a>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                            @endforeach
                                                            @if (Auth::user()->classes->count())
                                                            <li class="dropdown-submenu"><a href="#">Teacher's reports</a>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a href="/reports/heatmap_teacher/{{ Auth::user()->id }}/{{ $cycle->id }}">Summary - Heat Map</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="/reports/question_break_down_teacher/{{ Auth::user()->id }}/{{ $cycle->id }}">Detailed Question Breakdown</a>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                            @endif
                                                        </ul>
                                                        @endif
                                                        @if (Auth::user()->role == User::DEPARTMENT_HEAD)
                                                        <a href="/cycle/view/{{ $cycle->id }}">{{ $cycle->name }}</a>
                                                        <ul class="dropdown-menu">
                                                            <li class="dropdown-submenu"><a href="#">Head of Department's reports</a>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a href="/reports/heatmap_department_head/{{ Auth::user()->department->id }}/{{ $cycle->id }}">Summary - Heat Map</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="/reports/scatter_plot_department_head/{{ Auth::user()->department->id }}/{{ $cycle->id }}">Scatter Report</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="/reports/bar_graph_department_head/{{ Auth::user()->department->id }}/{{ $cycle->id }}">Bar Graph</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="/reports/question_break_down_department_head/{{ Auth::user()->department->id }}/{{ $cycle->id }}">Detailed Question Breakdown</a>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                            @if (Auth::user()->classes->count())
                                                            <li class="dropdown-submenu"><a href="#">Teacher's reports</a>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a href="/reports/heatmap_teacher/{{ Auth::user()->id }}/{{ $cycle->id }}">Summary - Heat Map</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="/reports/question_break_down_teacher/{{ Auth::user()->id }}/{{ $cycle->id }}">Detailed Question Breakdown</a>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                            @endif
                                                        </ul>
                                                        @endif
                                                        @if (Auth::user()->role == User::TEACHER)
                                                            <a href="#">{{ $cycle->name }}</a>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a href="/reports/heatmap_teacher/{{ Auth::user()->id }}/{{ $cycle->id }}">Summary - Heat Map</a>
                                                                </li>
                                                                <li>
                                                                    <a href="/reports/question_break_down_teacher/{{ Auth::user()->id }}/{{ $cycle->id }}">Detailed Question Breakdown</a>
                                                                </li>
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                @endif
                                <li class="{{ starts_with(Route::current()->getName(), 'help') ? 'active' : ''; }}">
                                    <a href="/help">Help</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="row">
                <div id="content" class="col-md-12">
                
                    <div id="inner-content">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </body>
    {{ $jsfiles }}
    <script type="text/javascript">
    {{ $jscalls}}
	
	
	/*setTimeout(function(){
	
		var container = document.getElementById('content');
		var groups = container.getElementsByTagName('g');
		for(var i=0; i<groups.length; i++) {
			if(groups[i].getAttributeNS(null, 'class') === 'highcharts-button') {
						groups[i].parentNode.removeChild(groups[i]);
			}
		}
		
	},500);*/
	
    </script>
    
<?php 

function get_browsername() {
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE){
$browser = 'Microsoft Internet Explorer';
}elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE) {
$browser = 'Google Chrome';
}elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE) {
$browser = 'Mozilla Firefox';
}elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== FALSE) {
$browser = 'Opera';
}elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== FALSE) {
$browser = 'Apple Safari';
}else {
$browser = 'error'; //<-- Browser not found.
}
return $browser;
}

$browser = get_browsername(); //<-- Display the browser name

//echo $browser; || $browser == 'Apple Safari'

if($browser == 'Mozilla Firefox' || $browser == 'Microsoft Internet Explorer' || $browser == 'error') {
	 
// Use the function to check
?>
{{ HTML::style('css/firefox.css?v=1') }}
<?php

}
elseif($browser == 'Apple Safari') { 
// Use the function to check
?>
{{ HTML::style('css/safari.css?v=1') }}
<?php

} 
else
{
?>
{{ HTML::style('css/chrome.css?v=1') }}
<?php	
}

?>    
    
</html>