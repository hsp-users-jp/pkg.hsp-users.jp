<?php

class Model_Package extends \Orm\Model_Soft
{
	protected static $_primary_key = array('revision_id');

	protected static $_properties = array(
		'revision_id',
		'id', // link to Model_Package_Base->id
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

	public function save($cascade = null, $use_transaction = false)
	{
		if (!$this->is_new())
		{ // 強制的に追記するように変更
			$this->_is_new     = true;
			foreach (self::$_primary_key as $key)
			{
				$this->_data[$key] = null;
			}
		}

		if (is_null($this->id))
		{ // 新しいパッケージとして生成
			$base = new Model_Package_Base();
			if (!$base->save())
			{
				return false;
			}

			$this->id = $base->id;
			$this->user_id = 1;
		}

		return parent::save($cascade, $use_transaction);
	}

	public function overwrite($cascade = null, $use_transaction = false)
	{
		if ($this->is_new() &&
			is_null($this->id))
		{ // 新しいパッケージとして生成
			$base = new Model_Package_Base();
			if (!$base->save())
			{
				return false;
			}

			$this->id = $base->id;
			$this->user_id = 1;
		}

		return parent::save($cascade, $use_transaction);
	}

	public static function query($options = array())
	{
		$subQuery
			= DB::select(DB::expr('MAX(revision_id)'))
				->from(self::table())
				->group_by('id');
		return
			parent::query($options)
				->where('revision_id', 'in', DB::expr('(' . $subQuery->__toString() . ')'));
	}

	public static function count(array $options = array())
	{
		return self::query($options)->count();
	}

	public static function find_by_id($id)
	{
		return
			parent::query()
				->where('id', $id)
				->order_by('revision_id', 'desc')
				->limit(1)
				->get_one();
	}

	public static function find_revision($id)
	{
		return
			parent::query()
				->where('id', $id)
				->order_by('revision_id', 'desc')
				->get();
	}

	public static function order_by_recent_update()
	{
		return
			self::query()
				->order_by('updated_at', 'desc');
	}

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
