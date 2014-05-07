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
		'requestFails' => array(
			"head" => "",
			'body' => ' Buchen Sie jetzt eins unserer Optimierungspakete und bringen Sie Ihre Website bei Google nach vorne'
		),
		'foundInResults' => array(
			"head" => "",
			"body" => 'Ihre Seite ist mit den angegebenen keywords: "%s" bei Google aktuell gut platziert- erhalten Sie diese Position und nutzen Sie dafür unseren Service
		'
		) ,
		'notFoundInResults' => array(
			"head" => 'Ihre Seite ist mit den angegebenen keywords: "%s" nicht bei Google unter den TOP8',
			"body" => "Sie können jetzt sofort etwas unternehmen und unseren Service für Sich gewinnbringend einsetzen",
		) 
	)
));
