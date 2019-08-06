<?php
Route::filter('department', function($route, $request) {
    // admins can do anything
    if (!Auth::user()->isAdministrator()) {
        $department = $route->getParameter('department');

        if (! Pivotal\Department\Controllers\DepartmentController::can_access($department)) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
        }
    }
});

