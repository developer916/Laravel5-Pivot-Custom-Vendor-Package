<div class="panel panel-pivot">
    <div class="panel-heading">
        Survey cycles
        @if (Auth::user()->isSuperAdmin())
            {{ link_to("/cycle/edit/$school->id", '', array('class'=>'glyphicon glyphicon-plus-sign', 'title'=>'Add survey cycle')); }}
            <span class="glyphicon glyphicon-question-sign help_button hidden-print" data-toggle="modal" data-target="#CycleViewModal" >
            </span>
            <div class="modal fade" id="CycleViewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Help</h4>
                        </div>
                        <div class="modal-body">
                            {{ $cycleviewmodal }}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="panel-body">
        <table class="table table-condensed table-hover">
            @foreach($cycles as $cycle)
                <tr>
                    <td>
                        @if (\Pivotal\Cycle\Controllers\CycleController::can_access($cycle))
                            {{ link_to("/cycle/view/$cycle->id", $cycle->name); }}
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
                            {{$cycle->reminded}}
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