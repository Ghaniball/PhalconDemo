<?php

$loader = new \Phalcon\Loader();

$_namespaces = require __DIR__ . '/../../vendor/composer/autoload_namespaces.php';

$namespaces = array();

foreach ($_namespaces as $namespace => $path) {
	$namespaces[substr($namespace, 0, -1)] = $path[0] . '/' . str_replace('\\', '/', $namespace);
}

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(array(
	$config->application->controllersDir,
	//$config->application->libraryDir,
	$config->application->formsDir,
))->registerNamespaces($namespaces)->register();
