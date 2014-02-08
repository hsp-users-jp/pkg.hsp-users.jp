<?php

class Model_Package extends \Orm\Model_Temporal
{
	protected static $_primary_key = array(
		'id',
		'temporal_start',
		'temporal_end'
	);

	protected static $_properties = array(
		'id',
		'revision',
		'user_id',
		'name',
		'path',
		'original_name',
		'version',
		'url',
		'description',
		'license_id',
		'package_type_id',
		'temporal_start',
		'temporal_end',
		'created_at',
		'updated_at',
	);

	protected static $_temporal = array(
		'start_column' => 'temporal_start',
		'end_column' => 'temporal_end',
		'mysql_timestamp' => true,
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
			'events' => array('before_save')
		),
		'Orm\\Observer_Self' => array(
			'events' => array('before_insert')
		),
	);

	protected static $_soft_delete = array(
		'mysql_timestamp' => true,
	);
	protected static $_table_name = 'packages';

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

	public function _event_before_insert()
	{
		// 一意なIDを生成
		do {
			$this->revision = Str::lower(Str::random('alnum', 16));
		} while (Model_Package::query()
					->where('revision', $this->revision)
					->count());
	}

	public static function query($options = array())
	{ // あらかじめ最新の履歴のみを対象とするように制限を掛ける
		$query_time =
			Date::forge()->format('%Y-%m-%d %H:%M:%S');
	//		\Config::get('orm.sql_max_timestamp_unix');
		return
			parent::query()
				->where('temporal_start', '<=', $query_time)
				->where('temporal_end',   '>=', $query_time);
	}

	public static function order_by_recent_update()
	{
		return
			Model_Package::query()
				->order_by('updated_at', 'desc');
	}
}
