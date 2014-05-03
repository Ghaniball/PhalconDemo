<?php

return new \Phalcon\Config(array(
/*	'database' => array(
		'adapter'     => 'Mysql',
		'host'        => 'localhost',
		'username'    => 'root',
		'password'    => '',
		'dbname'      => 'test',
	),*/
	'logPath' => __DIR__ . '/../../logs/',
	'application' => array(
		'controllersDir' => __DIR__ . '/../../app/controllers/',
		'modelsDir'      => __DIR__ . '/../../app/models/',
		'viewsDir'       => __DIR__ . '/../../app/views/',
		'pluginsDir'     => __DIR__ . '/../../app/plugins/',
		'libraryDir'     => __DIR__ . '/../../app/library/',
		'cacheDir'       => __DIR__ . '/../../app/cache/',
		'baseUri'        => '/',
	),
	'mail' => array(
                'fromName' => 'Phalcon Term',
                'fromEmail' => 'ghanibalx@gmail.com',
                'smtp' => array(
                        'server'	=> 'smtp.gmail.com',
                        'port' 		=> 465,
                        'security' => 'ssl',
                        'username' => 'ghanibalx@gmail.com',
                        'password' => '',
                )
        ),
));
