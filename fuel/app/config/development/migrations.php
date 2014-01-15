<?php
return array(
	'version' => 
	array(
		'app' => 
		array(
			'default' => 
			array(
				0 => '001_create_packages',
				1 => '002_create_package_types',
				2 => '003_create_package_versions',
				3 => '004_create_package_commons',
				4 => '005_create_package_screenshots',
				5 => '006_create_licenses',
				6 => '007_create_working_reports',
				7 => '008_create_working_requirements',
				8 => '009_create_hsp_categories',
				9 => '010_create_hsp_specifications',
				10 => '011_seeds_package_types',
				11 => '012_seeds_licenses',
				12 => '013_seeds_hsp_categories',
				13 => '014_seeds_hsp_specifications',
			),
		),
		'module' => 
		array(
		),
		'package' => 
		array(
			'auth' => 
			array(
				0 => '001_auth_create_usertables',
				1 => '002_auth_create_grouptables',
				2 => '003_auth_create_roletables',
				3 => '004_auth_create_permissiontables',
				4 => '005_auth_create_authdefaults',
				5 => '006_auth_add_authactions',
				6 => '007_auth_add_permissionsfilter',
				7 => '008_auth_create_providers',
				8 => '009_auth_create_oauth2tables',
				9 => '010_auth_fix_jointables',
			),
		),
	),
	'folder' => 'migrations/',
	'table' => 'migration',
);
