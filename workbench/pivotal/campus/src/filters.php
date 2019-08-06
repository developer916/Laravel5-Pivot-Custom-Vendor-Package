<?php
Route::filter('campus', function($route, $request) {
    // admins can do anything
    if (!Auth::user()->isAdministrator()) {
        $campus = $route->getParameter('campus');

        if (! Pivotal\Campus\Controllers\CampusController::can_access($campus)) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
        }
    }
});

