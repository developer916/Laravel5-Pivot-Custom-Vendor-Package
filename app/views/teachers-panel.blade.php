<div class="panel panel-pivot">
    <div class="panel-heading">
        Teachers
        @if (Auth::user()->isEditor())
            {{ link_to("/user/create/$school->id", '', array('class'=>'glyphicon glyphicon-plus-sign', 'title'=>'Add teacher')); }}
            <span class="glyphicon glyphicon-question-sign help_button" data-toggle="modal" data-target="#UserViewModal" >
            </span>
            <div class="modal fade" id="UserViewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Help</h4>
                        </div>
                        <div class="modal-body">
                            {{ $usersviewmodal }}
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
            @foreach($teachers as $user)
                <tr>
                    <td>
                        @if (UsersController::can_access($user))
                            {{ link_to("/user/view/$user->id", $user->name); }}
                        @else
                            {{ $user->name }}
                        @endif
                    </td>
                    @if (Auth::user()->editor)
                        <td>
                            {{ link_to("/user/edit/$user->id", '', array('class'=>'glyphicon glyphicon-edit', 'title'=>'Edit user')); }}&nbsp;
                            {{ link_to("/user/delete/$user->id", '', array('class'=>'glyphicon glyphicon-remove', 'title'=>'Delete user',
                                'data-toggle'=>'confirmation', 'data-placement' => 'left')); }}
                        </td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>
</div>