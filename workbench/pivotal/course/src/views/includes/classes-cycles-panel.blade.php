<div class="panel panel-pivot">
    <div class="panel-heading">
        Survey cycles
    </div>
    <div class="panel-body">
        @if ($class->cycles->count())
            <table class="table table-condensed table-hover">
                <tr>
                    <th>Cycle</th>
                    <th>Dates</th>
                    <th>Survey</th>
                    <th>Responses</th>
                    <th>Response rate</th>
                    @if (Auth::user()->isAdministrator())
                        <th></th>
                    @endif
                </tr>
                @foreach ($class->cycles as $cycle)
                    <tr>
                        <td>
                            @if (\Pivotal\Cycle\Controllers\CycleController::can_access($cycle) && !Auth::user()->isTeacher())
                                {{ link_to("/cycle/view/$cycle->id", $cycle->name); }}
                            @else
                                {{ $cycle->name }}
                            @endif
                        </td>
                        <td>
                            {{ $cycle->start_date; }} - {{ $cycle->end_date; }}
                        </td>
                        <td>
                            {{ $cycle->pivot->url }}
                        </td>
                        <td>
                            {{ $cycle->pivot->responses; }} / {{ $class->num_students; }}
                        </td>
                        <td>
                            {{ round(($cycle->pivot->responses/$class->num_students)*100, 0); }}%
                        </td>
                        @if (Auth::user()->isAdministrator())
                            <td>
                                {{ link_to($cycle->pivot->adminurl, '', array('class'=>'glyphicon glyphicon-edit', 'title'=>'Edit survey', 'target'=>'_blank')); }}&nbsp;
                            </td>
                        @endif
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
</div>