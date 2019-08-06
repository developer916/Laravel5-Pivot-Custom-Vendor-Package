<?php

return array(

	'driver' => 'sendmail',

	'from' => array('address' => 'notifications@pivotpl.com', 'name' => 'Pivot Professional Learning'),

	'encryption' => 'tls',

	'sendmail' => '/usr/sbin/sendmail -bs',

	'pretend' => false,

);
