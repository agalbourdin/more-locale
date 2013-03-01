<?php
\Agl\Core\ConfigLoader::add(
	'more/locale',
	array(
		'config' => __DIR__ . DIRECTORY_SEPARATOR . 'config.json',
		'events' => __DIR__ . DIRECTORY_SEPARATOR . 'events.json',
		'locale' => realpath(__DIR__ . '/../' . DIRECTORY_SEPARATOR . 'locale') . DIRECTORY_SEPARATOR
	)
);
