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

	protected static $_many_many = array(
		'screenshots' => array(
			'key_from' => 'revision_id',
			'key_through_from' => 'package_revision_id',
			'table_through' => 'package_revisions_screenshots',
			'key_through_to' => 'package_screenshot_id',
			'model_to' => 'Model_Package_Screenshot',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
//		'working_requirements' => array(
//			'key_from' => 'revision_id',
//			'key_through_from' => 'package_revision_id',
//			'table_through' => 'package_revisions_requirements',
//			'key_through_to' => 'working_requirement_id',
//			'model_to' => 'Model_Package_Screenshot',
//			'key_to' => 'id',
//			'cascade_save' => true,
//			'cascade_delete' => false,
//		),
	);

	protected static $_belongs_to = array(
		'base' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Package_Base',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
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

	private $cancel_function_overwrite = false;

	public function save($cascade = null, $use_transaction = false)
	{
		if (!$this->is_new() &&
			!$this->cancel_function_overwrite)
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
			$this->user_id = 1; // デバッグ用
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
			$this->user_id = 1; // デバッグ用
		}

		return parent::save($cascade, $use_transaction);
	}

	protected function delete_self()
	{
		$this->cancel_function_overwrite = true;
		$r = parent::delete_self();
		$this->cancel_function_overwrite = false;
		return $r;
	}

	public function restore($cascade_restore = null)
	{
		$this->cancel_function_overwrite = true;
		$r = parent::restore($cascade_restore);
		$this->cancel_function_overwrite = false;
		return $r;
	}

	public function destroy()
	{
		$pkg = Model_Package_Base::find($this->id);
		if ($pkg)
		{
			$pkg->delete();
		}
		return null != $pkg;
	}

	public function cure()
	{
		$pkg = Model_Package_Base::deleted($this->id);
		if ($pkg)
		{
			$pkg->undelete();
		}
		return null != $pkg;
	}

	public static function query($options = array())
	{
		$subQuery
			= DB::select(DB::expr('MAX(revision_id)'))
				->from(self::table())
				->group_by('id');

		if (!Auth::is_super_admin())
		{ // 管理者の場合Banされているユーザーも表示する
			$query = parent::query($options)
						->related(array('user', 'base'))
						->where('user.group_id', '!=', Auth::get_group_by_name('Banned')->id)
						->where('base.id', '!=', null);
		}
		else
		{
			self::disable_filter();
			$query = parent::query($options);
		}

		return
			$query
				->where('revision_id', 'in', DB::expr('(' . $subQuery->__toString() . ')'));
	}

	public static function count(array $options = array())
	{
		return self::query($options)->count();
	}

	public static function find($revision_id = null, array $options = array())
	{
		$properties = array();
		foreach (array_keys(self::properties()) as $property_) {
			$properties[] = self::table().'.'.$property_;
		}
		$query = DB::select_array($properties)
					->from(self::table())
					->join(\Auth\Model\Auth_User::table(), 'LEFT')
						->on(\Auth\Model\Auth_User::table().'.id', '=', self::table().'.user_id')
					->join(Model_Package_Base::table(), 'LEFT')
						->on(Model_Package_Base::table().'.id', '=', self::table().'.id')
					;
		if (!Auth::is_super_admin())
		{ // 管理者の場合Banされているユーザーも表示する
			$query = $query
						->where(\Auth\Model\Auth_User::table().'.group_id', '!=', Auth::get_group_by_name('Banned')->id)
						->where(Model_Package_Base::table().'.deleted_at', '=', null)
						->where(self::table().'.deleted_at', '=', null)
						;
		}
		if (null !== $revision_id)
		{
			$query = $query
						->where(self::table().'.revision_id', $revision_id);
		}
		$result = $query
					->as_object('Model_Package')
					->execute()
					->as_array()
					;

		return !empty($result)
				? null !== $revision_id
					? $result[0]
					: $result
				: null;
	}

	public static function find_by_id($id)
	{
		if (!Auth::is_super_admin())
		{ // 管理者の場合Banされているユーザーも表示する
			$query = parent::query()
						->related(array('user', 'base'))
						->where('user.group_id', '!=', Auth::get_group_by_name('Banned')->id)
						->where('base.id', '!=', null);
		}
		else
		{
			self::disable_filter();
			$query = parent::query();
		}

		return
			$query
				->where('id', $id)
				->order_by('revision_id', 'desc')
				->limit(1)
				->get_one();
	}

	public static function find_revision($id)
	{
		if (!Auth::is_super_admin())
		{ // 管理者の場合Banされているユーザーも表示する
			$query = parent::query()
						->related(array('user', 'base'))
						->where('user.group_id', '!=', Auth::get_group_by_name('Banned')->id)
						->where('base.id', '!=', null);
		}
		else
		{
			self::disable_filter();
			$query = parent::query();
		}

		return
			$query
				->where('id', $id)
				->order_by('revision_id', 'desc')
				->get();
	}

	public static function order_by_recent_update()
	{
		return
			self::query()
				->order_by('updated_at', 'desc')
				->order_by('created_at', 'desc')
				;
	}

	// 指定のパッケージのリビジョンの数を取得
	public static function count_of_revision($id)
	{
		if (Auth::is_super_admin())
		{
			self::disable_filter();
		}

		$query = parent::query();

		return $query
				->where('id', $id)
				->where('deleted_at', '=', null)
				->count();
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
