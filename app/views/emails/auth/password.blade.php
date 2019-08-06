<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
        Hi {{$user->name}},<br/>
        <p>You have been subscribed to the Pivot Professional Learning survey tool.</p>

        <p>You can login to the system by following the link: {{ Config::get('app.url') }}</p>
        <p>
            Username: {{$user->email}} <br/>
            Password: {{$password}} <br/>
        </p>
        <p>
            You can change your password by clicking the 'Reset your password' link on the opening screen. Any questions, please contact your school administrator.
        </p>
        <p>
            Kind regards<br />
            The Pivot Team
        </p>
    </body>
</html>