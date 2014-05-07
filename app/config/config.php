<?php

$mailConfig = require APP_DIR . '/config/mail.php';

return new \Phalcon\Config(array(
	/* 	'database' => array(
	  'adapter'     => 'Mysql',
	  'host'        => 'localhost',
	  'username'    => 'root',
	  'password'    => '',
	  'dbname'      => 'test',
	  ), */
	//'baseUrl' => 'http://192.168.3.134/PhalconDemo/public/',
	'baseUrl' => '',
	'logPath' => __DIR__ . '/../../logs/',
	'application' => array(
		'controllersDir' => APP_DIR . '/controllers/',
		'modelsDir' => APP_DIR . '/models/',
		'viewsDir' => APP_DIR . '/views/',
		'pluginsDir' => APP_DIR . '/plugins/',
		'formsDir' => APP_DIR . '/forms/',
		'libraryDir' => APP_DIR . '/library/',
		'cacheDir' => APP_DIR . '/cache/',
		'baseUri' => '/',
	),
	'mail' => $mailConfig,
	'messages' => array(
		'requestFails' => 'You came to the right address! We see you have the potential of improving your position in the Google search for the "%s" keyword you specified!',
		'foundInResults' => 'Domain mentioned is Detected in the first 8 positions of the "%s" request based on local search (Google.de) and German Language We see that you already did a good job positioning yourself for the Keyword specified in the first 8 positions of the Google.de search and we will be glad to help you improve your position for other important Keywords or improve your actual positions locally and internationally!',
		'notFoundInResults' => 'Domain mentioned is NOT detected in the first 8 positions of the "%s" request based on local search (Google.de) and German Language You came to the right address! Being out of first 8 positions in the Google search for the important Keyword you specified we can definitely help you here!'
	)
));
