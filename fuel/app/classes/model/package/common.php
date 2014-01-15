<?php

class Model_Package_Common extends \Orm\Model_Soft
{
	protected static $_properties = array(
		'id',
		'package_id',
		'package_type_id',
		'name',
		'url',
		'description',
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
	protected static $_table_name = 'package_commons';

	protected static $_belongs_to = array(
		'type' => array(
			'key_from' => 'package_type_id',
			'model_to' => 'Model_Package_Type',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);
}
