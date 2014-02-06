<?php

class Model_Package_Version extends \Orm\Model_Soft
{
	protected static $_properties = array(
		'id',
		'package_id',
		'license_id',
		'path',
		'original_name',
		'version',
		'created_at',
		'updated_at',
		'deleted_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => true,
		),
	);

	protected static $_soft_delete = array(
		'mysql_timestamp' => true,
	);
	protected static $_table_name = 'package_versions';

	protected static $_belongs_to = array(
		'license' => array(
			'key_from' => 'license_id',
			'model_to' => 'Model_License',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);
}
