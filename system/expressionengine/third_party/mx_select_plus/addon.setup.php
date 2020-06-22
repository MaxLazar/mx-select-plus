<?php

require_once PATH_THIRD . 'mx_select_plus/config.php';
	
return array(
	'author' => MX_SELECT_AUTHOR,
	'author_url' => '',
	'description' => MX_SELECT_DESC,
	'docs_url' => MX_SELECT_DOCS,
	'fieldtypes' => array(
		'mx_select_plus' => array(
			'name' => MX_SELECT_NAME,
			//'compatibility' => 'text'
		)
	),
	'name' => MX_SELECT_NAME,
	'namespace' => '\\',
	'version' => MX_SELECT_VER
);