<div class="panel panel-pivot">
    <div class="panel-heading">
        @if (Auth::user()->isSuperAdmin())
            {{ $cycle->classes->count() }} of {{ $cycle->school->classesForYear($cycleYear)->count() }} classes in this cycle
        @endif
        @if (Auth::user()->isSuperAdmin())
            {{ link_to("/cycle/class/edit/{$cycle->id}", '', array('class'=>'glyphicon glyphicon-plus-sign', 'title'=>'Add classes to survey')); }}
        @endif
    </div>
    <div class="panel-body">
        <div class="survey_prev"><a href="{{ Config::get('limesurvey.surveyurl'); }}survey/index/sid/{{ Config::get('limesurvey.template'); }}/newtest/Y/lang/en" target="_blank">Survey Preview</a></div>
        @if ($cycle->classes->count())
            <table class="table table-condensed table-hover">
                <tr>
                    <th style="width:200px">Teacher</th>
                    <th>Class</th>
                    <th>Survey</th>
                    <th>Responses</th>
                    <th>Response rate</th>
                    @if (Auth::user()->isEditor())
                        <th></th>
                    @endif
                </tr>
                @foreach($cycle->classes as $class)
                    <tr>
                        <td>{{$class->teacher->name}}</td>
                        <td>
                            @if (Pivotal\Course\Controllers\CourseController::can_access($class))
                                {{ link_to("/class/view/$class->id", $class->name.' Yr'.$class->year_level.' ('.$class->code.')'); }}
                            @else
                                {{ $class->name.' Yr'.$class->year_level.' ('.$class->code.')' }}
                            @endif
                        </td>
                        <td>
                            {{ $class->pivot->url }}
                        </td>
                        <td>
                            {{ $class->pivot->responses; }} / {{ $class->num_students; }}
                        </td>
                        <td>
                            @if ($class->num_students > 0)
                                {{ round(($class->pivot->responses/$class->num_students)*100, 0); }}%
                            @else
                                &nbsp;
                            @endif
                        </td>
                        @if (Auth::user()->isSuperAdmin())
                            <td>
                                @if (Auth::user()->isSuperAdmin())
                                    {{ link_to($class->pivot->adminurl, '', array('class'=>'glyphicon glyphicon-edit', 'title'=>'Edit survey', 'target'=>'_blank')); }}&nbsp;
                                @endif
                                {{ link_to("/cycle/class/delete/{$cycle->id}/{$class->id}", '', array('class'=>'glyphicon glyphicon-remove', 'title'=>'Remove class',
                                    'data-toggle'=>'confirmation', 'data-placement' => 'left')); }}
                                @if ($class->num_students > $class->pivot->responses)
                                    {{ link_to("/cycle/class/fake/".$cycle->id.'/'.$class->id, 'Generate', array('class'=>'glyphicon glyphicon-refresh', 'title'=>'Generate fake responses')); }}&nbsp;
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
</div>