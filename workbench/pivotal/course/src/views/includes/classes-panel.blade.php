<div class="panel panel-pivot">
    <div class="panel-heading">
        Classes
        @if (Auth::user()->isEditor() && $school && !Request::is('user/view/*'))
            {{ link_to("/class/edit/{$school->id}", '', array('class'=>'glyphicon glyphicon-plus-sign', 'title'=>'Add class')); }}
            <span class="glyphicon glyphicon-question-sign help_button" data-toggle="modal" data-target="#ClassViewModal" >
            </span>
            <div class="modal fade" id="ClassViewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Help</h4>
                        </div>
                        <div class="modal-body">
                            {{ $classviewmodal }}
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
          <table class="table table-condensed table-hover" id="classesList">
            @foreach($classes as $class)
                <tr data-campus="{{ intval($class->campus_id) }}">
                    <td>
                        @if (Pivotal\Course\Controllers\CourseController::can_access($class))
                            {{ link_to("/class/view/$class->id", $class->name.' Yr'.$class->year_level.' ('.$class->code.')'); }}
                        @else
                            {{ $class->name.' Yr'.$class->year_level.' ('.$class->code.')' }}
                        @endif
                    </td>
                    @if (Auth::user()->isEditor())
                        <td>
                            {{ link_to("/class/edit/{$school->id}/{$class->department_id}/{$class->id}", '', array('class'=>'glyphicon glyphicon-edit', 'title'=>'Edit class')); }}&nbsp;
                            {{ link_to("/class/delete/{$class->id}", '', array('class'=>'glyphicon glyphicon-remove', 'title'=>'Delete class', 'data-toggle'=>'confirmation')); }}
                        </td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>
</div>