<?php
Route::filter('user', function ($route, $request) {
    // admins can do anything
    if (!Auth::user()->isAdministrator()) {
        $user = $route->getParameter('user');

        if (!Pivotal\User\Controllers\UserController::can_access($user)) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
        }
    }
});
