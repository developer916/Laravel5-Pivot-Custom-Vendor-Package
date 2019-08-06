<!DOCTYPE html>
<html>
    <head>
        {{ HTML::script('//code.jquery.com/jquery-1.10.2.min.js') }}
        {{ HTML::style('bootstrap/css/bootstrap.min.css') }}
        {{ HTML::script('bootstrap/js/bootstrap.min.js') }}
        {{ HTML::script('bootstrap/js/bootstrap-confirmation.js') }}
        {{ HTML::style('bootstrap-datepicker/css/datepicker3.css') }}
        {{ HTML::script('bootstrap-datepicker/js/bootstrap-datepicker.js') }}
        {{ HTML::style('select2-3.5.2/select2.css') }}
        {{ HTML::style('select2-3.5.2/select2-bootstrap.css') }}
        {{ HTML::script('select2-3.5.2/select2.min.js') }}
        {{ HTML::style('css/styles.css?v=1') }}
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
    <body>
        <div class="container">
            <div id="header_row" class="row">
                <div id="header_row" class="col-md-9">
                    <div id="header_div">
                        <h2 id="header"><span id="header_student">Pivot Professional Learning</span>
                        @if (Auth::check())
                            {{ link_to('/user/view/'.Auth::user()->id, Auth::user()->name, array('id'=>'subheader_bold')); }}
                                @if (Session::get('orig_id'))
                                    @if(Auth::user()->isAdministrator())
                                    <span class="hidden-print">({{ link_to('/logout_as/', 'Back to Pivot Admin'); }})</span>
                                    @endif
                                    <span class="hidden-print">({{ link_to('/logout_as/', 'Return To Your Account'); }})</span>
                                @else
                                    <span class="hidden-print">({{ link_to('/logout/', 'Logout'); }})</span>
                                @endif
                            {{ Auth::user()->school ? ' | '.Auth::user()->school->name : '' }}
                        @endif
                        </h2>
                    </div>
                </div>
                <div class="col-md-3 text-right">
                    <a href="/" title="Home">
                        {{ HTML::image('images/logo.png', 'logo') }}
                    </a>
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
                                        <a href="{{ route('user.index') }}">Teachers</a>
                                    </li>
                                @elseif (Auth::user()->school)
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
                                                            <li class="dropdown-submenu"><a href="#">My Teacher Report</a>
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
                                                            <li class="dropdown-submenu"><a href="#">Head of Department Reports</a>
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
                                                            <li class="dropdown-submenu"><a href="#">My Teacher Report</a>
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
                                <li class="{{ starts_with(Route::current()->getName(), 'resources') ? 'active' : ''; }}">
                                    <a href="/resource/byquestion">Resources</a>
                                </li>
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

        @if (isset($jsfiles))
            {{ $jsfiles }}
        @endif
        @if (isset($jscalls))
            <script type="text/javascript">
                {{ $jscalls }}
                $(function () {
                    // confirmation dialogs
                    $('[data-toggle="confirmation"]').each(function() {
                        $(this).attr('data-href', $(this).attr('href'));
                    });
                    $('[data-toggle="confirmation"]').confirmation({singleton:true});
                });
            </script>
        @endif
    </body>
</html>
