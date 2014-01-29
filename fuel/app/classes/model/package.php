<?php

class Model_Package extends \Orm\Model_Soft
{
	protected static $_properties = array(
		'id',
		'user_id',
		'package_common_id',
		'package_version_id',
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
	protected static $_table_name = 'packages';

	protected static $_has_many = array(
		'versions' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Package_Version',
			'key_to' => 'package_id',
			'cascade_save' => true,
			'cascade_delete' => true,
		),
		'screenshots' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Package_Screenshot',
			'key_to' => 'package_id',
			'cascade_save' => true,
			'cascade_delete' => true,
		),
	);

	protected static $_has_one = array(
		'version' => array(//うーん？循環になってしまう？
			'key_from' => 'package_version_id',
			'model_to' => 'Model_Package_Version',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'common' => array(
			'key_from' => 'package_common_id',
			'model_to' => 'Model_Package_Common',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'user' => array(
			'key_from' => 'user_id',
			'model_to' => '\\Auth\\Model\\Auth_User',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
	);

	public static function order_by_recent_update()
	{
		return
			Model_Package::query()
				->order_by('updated_at', 'desc');
	}
}
