<div class="panel panel-pivot">
    <div class="panel-heading">
        Departments
        @if (Auth::user()->isEditor() && $school && !Request::is('user/view/*'))
            {{ link_to("/department/edit/$school->id", '', array('class'=>'glyphicon glyphicon-plus-sign', 'title'=>'Add department')); }}
            <span class="glyphicon glyphicon-question-sign help_button" data-toggle="modal" data-target="#DeptViewModal" >
            </span>
            <div class="modal fade" id="DeptViewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Help</h4>
                        </div>
                        <div class="modal-body">
                            {{ $deptviewmodal }}
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
            @foreach($departments as $department)
                <tr>
                    <td>
                        @if (Pivotal\Department\Controllers\DepartmentController::can_access($department))
                            {{ link_to("/department/view/$department->id", $department->name); }}
                        @else
                            {{ $department->name }}
                        @endif
                    </td>
                    @if (Auth::user()->isEditor())
                        <td>
                            {{ link_to("/department/edit/$school->id/$department->id", '', array('class'=>'glyphicon glyphicon-edit', 'title'=>'Edit department')); }}&nbsp;
                            {{ link_to("/department/delete/$department->id", '', array('class'=>'glyphicon glyphicon-remove', 'title'=>'Delete department', 'data-toggle'=>'confirmation')); }}
                        </td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>
</div>