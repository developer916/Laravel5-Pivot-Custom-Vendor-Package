<?php
Route::filter('cycle', function($route, $request) {
    // admins can do anything
    if (!Auth::user()->isAdministrator()) {
        $cycle = $route->getParameter('cycle');

        if (! Pivotal\Cycle\Controllers\CycleController::can_access($cycle)) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
        }
    }
});

