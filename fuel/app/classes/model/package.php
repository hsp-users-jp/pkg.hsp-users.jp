<?php

class Model_Package
{
	protected static $_properties = array(
		'package_type_id',
		'name',
		'path',
		'original_name',
		'version',
		'license_id',
		'url',
		'description',
	);

	private $properties = array();
	private $base = null;
	private $revision = null;

	public function __construct()
	{
	}

	public function __get($name)
	{
		return $this->revision->{$name};
	}

	public function __set($name, $value)
	{
		if (!in_array($name, self::$_properties))
		{
			throw new Exception('undefind index: '.$name);
		}
		$this->properties[$name] = $value;
	}

	public function save()
	{
		if (!$this->base)
		{
			$this->base= new Model_Package_Base();
			if (!$this->base->save())
			{
				return false;
			}
		}

		$this->revision = new Model_Package_Revision();

		foreach ($this->properties as $key => $val)
		{
			$this->revision->{$key} = $val;
		}
		$this->revision->package_base_id = $this->base->id;
		$this->revision->user_id = 1; // テスト用
		$this->revision->save();

		if (!$this->revision->save())
		{
			return false;
		}

		return true;
	}

	public function overwrite()
	{
		if (!$this->base ||
			!$this->revision)
		{
			return false;
		}

		foreach ($this->properties as $key => $val)
		{
			$this->revision->{$key} = $val;
		}
		$this->revision->package_base_id = $this->base->id;
		$this->revision->user_id = 1; // テスト用
		$this->revision->save();

		if (!$this->revision->save())
		{
			return false;
		}

		return true;
	}

/*
	// パッケージIDでパッケージを取得
	public static function find_by_id($package_id)
	{
	}

	// リビジョンでパッケージを取得
	public static function find_by_revision($revision)
	{
	}

	// パッケージIDでバージョン履歴を取得
	public static function enum_revisions_by_id($package_id)
	{
	}
*/

	public static function order_by_recent_update()
	{
		return
			Model_Package_Revision::query()
				->order_by('updated_at', 'desc');
	}

	// 指定のユーザーがパッケージを持っているか？
	public static function has_package($userid = null)
	{
		if (is_null($userid))
		{
			$userid = Auth::get_user_id_only();
		}
		return
			0 < Model_Package_Revision::query()
				->where('user_id', $userid)
				->count();
	}
}
