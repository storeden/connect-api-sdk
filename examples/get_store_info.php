<?php

/**
 * Get Store basic information
 */
include '../src/autoload.php';

$config = [
	'api_key' => '',
	'api_exchange' => ''
];

$api = new Storeden\Storeden($config);

$_store = $api->get('/store/info.json');

echo '****** STORE INFO ******'.PHP_EOL;
echo 'ID: '.$_store->response->uid.PHP_EOL;
echo 'URL: '.$_store->response->url.PHP_EOL;
echo '************************'.PHP_EOL;
