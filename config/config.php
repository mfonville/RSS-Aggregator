<?php

$config = array(
	// Debug mode outputs the log messages instead of the feed.
	'debug_mode' => FALSE,

	// What level of message to record.
	// 0 = disabled
	// 1 = errors
	// 2 = warnings
	// 3 = information
	// 4 = debug
	'log_level' => 0,

	// Cache setup.
	'cache_enabled' => TRUE,
	'cache_expiry' => 1200,

	// Specify a SQLite extension version, or FALSE for auto-select.
	'sqlite_version' => FALSE,

	// This needs to be the URL that you'll use to access the script on the web.
	'url' => 'https://opengapps.org',

	// Feed title.
	'title' => 'Open GApps releases',

	// Specify your feeds here.
	'feeds' => array(
		'ARM' => 'https://github.com/opengapps/arm/releases.atom',
		'ARM64' => 'https://github.com/opengapps/arm64/releases.atom',
		'x86' => 'https://github.com/opengapps/x86/releases.atom',
		'x86_64' => 'https://github.com/opengapps/x86_64/releases.atom',
	),
);
