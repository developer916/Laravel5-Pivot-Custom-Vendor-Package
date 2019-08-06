<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Password Reset</h2>
        Hello,<br/>
        <p>You recently requested to reset your Pivot Professional Learning password.</p>

		<p>
			To reset your password, complete this form: {{ URL::to('password/reset', array($token)) }}.<br/>
			This link will expire in {{ Config::get('auth.reminder.expire', 60) }} minutes.
		</p>
        <p>If you did not request to reset your password, rest assured that your account is secure.<br/>
            Password changes requested through our website are only sent to the contact email on the account.<br/>
            If you have any questions, please contact your school administrator.
        </p>
        <p>
            Kind regards<br />
            The Pivot Team
        </p>
	</body>
</html>
