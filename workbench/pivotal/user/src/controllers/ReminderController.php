<?php namespace Pivotal\User\Controllers;

use Pivotal\User\Controllers\BaseReminderController;
use \Input;
use \Lang;
use \Password;
use \Redirect;
use \View;
use \Hash;

class ReminderController extends BaseReminderController
{

    /**
     * Display the password reminder view.
     *
     * @return Response
     */
    public function view()
    {
        $data = ['header' => 'Reset my password'];

        return View::make('remind-view', $data);
    }

    /**
     * Handle a POST request to remind a user of their password.
     *
     * @return Response
     */
    public function send()
    {

        $response = Password::remind(Input::only('email'), function ($message) {
            // TODO customise the reminder email in /app/views/emails/auth/reminder.blade.php
            $message->subject('Reset Password');
        });

        switch ($response) {
            case Password::INVALID_USER:
                return Redirect::back()->withErrors(['email' => Lang::get($response)]);

            case Password::REMINDER_SENT:
                return Redirect::back()->with('message', Lang::get($response));
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string $token
     * @return Response
     */
    public function reset_view($token = null)
    {

        if (is_null($token)) App::abort(404);

        $data = [
            'header' => 'Reset password',
            'jsfiles' => '<script type="text/javascript" src="/javascript/pwstrength-bootstrap-1.2.10.js"></script>',

        ];

        return View::make('remind-reset', $data)->with('token', $token);
    }

    /**
     * Handle a POST request to reset a user's password.
     *
     * @return Response
     */
    public function reset_process()
    {

        $credentials = Input::only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        switch ($response) {
            case Password::INVALID_USER:
                return Redirect::back()->withErrors(['email' => Lang::get($response)]);

            case Password::INVALID_PASSWORD:
                return Redirect::back()->withErrors(['password' => Lang::get($response)]);

            case Password::INVALID_TOKEN:
                return Redirect::back()->with('error', Lang::get($response));

            case Password::PASSWORD_RESET:
                return Redirect::to('/login');
        }
    }
}