<?php
Route::filter('course', function($route, $request) {
    // admins can do anything
    if (!Auth::user()->isAdministrator()) {
        $course = $route->getParameter('course');

        if (! Pivotal\Course\Controllers\CourseController::can_access($course)) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
        }
    }
});

