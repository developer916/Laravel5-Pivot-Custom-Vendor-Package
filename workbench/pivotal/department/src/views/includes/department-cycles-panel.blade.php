<div class="panel panel-pivot">
    <div class="panel-heading">
        Survey cycles
    </div>
    <div class="panel-body">
        <table class="table table-condensed table-hover">
            @foreach($cycles as $cycle)
                <tr>
                    <td>
                        @if (\Pivotal\Cycle\Controllers\CycleController::can_access($cycle))
                            {{ link_to("/cycle/departmentview/$cycle->id/$department->id", $cycle->name); }}
                        @else
                            {{ $cycle->name }}
                        @endif
                    </td>
                    <td>
                        {{ $cycle->start_date; }} -
                        {{ $cycle->end_date; }}
                    </td>
                    @if (Auth::user()->isSuperAdmin())
                        <td>
                            {{ link_to("/cycle/edit/$school->id/$cycle->id", '', array('class'=>'glyphicon glyphicon-edit', 'title'=>'Edit cycle')); }}&nbsp;
                            {{ link_to("/cycle/delete/$cycle->id", '', array('class'=>'glyphicon glyphicon-remove', 'title'=>'Delete cycle',
                                'data-toggle'=>'confirmation', 'data-placement' => 'left')); }}
                        </td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>
</div>