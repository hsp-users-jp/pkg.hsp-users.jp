<?php

class Model_Package_Base extends \Orm\Model_Soft
{
	protected static $_primary_key = array('id');

	protected static $_properties = array(
		'id',
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
	protected static $_table_name = 'package_bases';

	protected static $_has_many = array(
//		'screenshots' => array(
//			'key_from' => array('id', , ),
//			'model_to' => 'Model_Package_Screenshot',
//			'key_to' => 'package_base_id',
//			'cascade_save' => false,
//			'cascade_delete' => false,
//		),
//		'revisions' => array(
//			'key_from' => 'id',
//			'model_to' => 'Model_Package_Revision',
//			'key_to' => 'package_base_id',
//			'cascade_save' => false,
//			'cascade_delete' => false,
//		),
	);
}
