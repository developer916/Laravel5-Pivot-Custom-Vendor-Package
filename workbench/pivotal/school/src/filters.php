<?php
Route::filter('school', function ($route, $request) {
    // admins can do anything
    if (!Auth::user()->isAdministrator()) {
        $school = $route->getParameter('school');
        if (is_null($school)) {
            $school = School::where('id', '=', Auth::user()->school_id)->first();
        }

        if (!Pivotal\School\Controllers\SchoolController::can_access($school)) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
        }
    }
});
