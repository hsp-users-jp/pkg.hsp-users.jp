<?php

class Model_Package_Revision extends \Orm\Model_Soft
{
	protected static $_properties = array(
		'id',
		'package_base_id',
		'package_type_id',
		'user_id',
		'name',
		'path',
		'original_name',
		'version',
		'license_id',
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
		'Observer_UserId' => array(
			'events' => array('before_insert')
		),
	);

	protected static $_soft_delete = array(
		'mysql_timestamp' => true,
	);
	protected static $_table_name = 'package_revisions';

	protected static $_has_many = array(
//		'screenshots' => array(
//			'key_from' => array('id', , ),
//			'model_to' => 'Model_Package_Screenshot',
//			'key_to' => 'package_id',
//			'cascade_save' => false,
//			'cascade_delete' => false,
//		),
	);

	protected static $_belongs_to = array(
		'user' => array(
			'key_from' => 'user_id',
			'model_to' => '\\Auth\\Model\\Auth_User',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
		'license' => array(
			'key_from' => 'license_id',
			'model_to' => 'Model_License',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
		'type' => array(
			'key_from' => 'package_type_id',
			'model_to' => 'Model_Package_Type',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);

	// 指定のユーザーがパッケージを持っているか？
	public static function has_package($userid = null)
	{
		if (is_null($userid))
		{
			$userid = Auth::get_user_id_only();
		}
		return 0 < self::query()->where('user_id', $userid)->count();
	}
}
