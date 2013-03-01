<?php
ConfigLoader::add(
	'more/locale',
	array(
		'config' => realpath('config.json'),
		'events' => realpath('events.json')
	)
);
