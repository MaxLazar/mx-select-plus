<?php

$addonJson = json_decode(file_get_contents(__DIR__ . '/addon.json'));


if (!defined('MX_SELECT_NAME')) {
    define('MX_SELECT_NAME', $addonJson->name);
    define('MX_SELECT_KEY', $addonJson->shortname);
    define('MX_SELECT_VERSION', $addonJson->version);
    define('MX_SELECT_DOCS', '');
    define('MX_SELECT_DESCRIPTION', $addonJson->description);
    define('MX_SELECT_AUTHOR', 'Max Lazar');
    define('MX_SELECT_DEBUG', false);
}

return [
    'name' => $addonJson->name,
    'description' => $addonJson->description,
    'version' => $addonJson->version,
    'namespace' => $addonJson->namespace,
    'author' => 'Max Lazar',
    'author_url' => 'http://www.eec.ms/add-on/mx-select-plus',
    'settings_exist' => false,
    // Advanced settings
    'services' => [],
	
	'fieldtypes' => array(
		'mx_select_plus' => array(
			//'name' => MX_SELECT_NAME,
			'compatibility' => 'list'
		)
	),
];
